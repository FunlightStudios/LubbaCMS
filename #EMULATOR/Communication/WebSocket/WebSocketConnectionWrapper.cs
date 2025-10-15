using System;
using System.Text;
using Fleck;
using log4net;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace Plus.Communication.WebSocket
{
    /// <summary>
    /// Wrapper for WebSocket connection to integrate with existing GameClient system
    /// </summary>
    public class WebSocketConnectionWrapper : IDisposable
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.WebSocket");
        private static int _connectionIdCounter = 0;

        private readonly IWebSocketConnection _socket;
        private readonly string _ip;
        private readonly int _connectionId;
        private bool _disposed;

        public delegate void DataReceivedHandler(byte[] data);
        public delegate void ConnectionClosedHandler();

        public event DataReceivedHandler OnDataReceived;
        public event ConnectionClosedHandler OnConnectionClosed;

        public int ConnectionId => _connectionId;
        public string IP => _ip;
        public bool IsConnected => _socket?.IsAvailable ?? false;

        public WebSocketConnectionWrapper(IWebSocketConnection socket, string ip)
        {
            _socket = socket ?? throw new ArgumentNullException(nameof(socket));
            _ip = ip;
            _connectionId = System.Threading.Interlocked.Increment(ref _connectionIdCounter);
            _disposed = false;
        }

        /// <summary>
        /// Handle incoming text message from Nitro client (not used - Nitro uses binary)
        /// </summary>
        public void HandleIncomingMessage(string message)
        {
            try
            {
                log.Debug($"Received text message (unexpected): {message}");
            }
            catch (Exception ex)
            {
                log.Error($"Error handling Nitro message: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle incoming binary message from Nitro client
        /// </summary>
        public void HandleIncomingBinary(byte[] data)
        {
            try
            {
                // Nitro uses BINARY protocol, not JSON!
                OnDataReceived?.Invoke(data);
            }
            catch (Exception ex)
            {
                log.Error($"Error handling binary message: {ex.Message}");
            }
        }

        /// <summary>
        /// Send binary data to client (Nitro expects binary packets)
        /// </summary>
        public void SendData(byte[] data)
        {
            if (_disposed || !IsConnected)
                return;

            try
            {
                // Send as binary data (Nitro protocol)
                _socket.Send(data);
                
                log.Debug($"Sent {data.Length} bytes to Nitro client");
            }
            catch (Exception ex)
            {
                log.Error($"Error sending data to Nitro client: {ex.Message}");
            }
        }

        /// <summary>
        /// Disconnect the WebSocket
        /// </summary>
        public void Disconnect()
        {
            if (_disposed)
                return;

            try
            {
                _socket?.Close();
                OnConnectionClosed?.Invoke();
            }
            catch (Exception ex)
            {
                log.Error($"Error disconnecting WebSocket: {ex.Message}");
            }
        }

        /// <summary>
        /// Convert Nitro JSON packet to binary format for existing packet system
        /// </summary>
        private byte[] ConvertJsonToBinary(JObject json)
        {
            try
            {
                // Nitro packet structure: {"header": 4000, "data": {...}}
                if (!json.ContainsKey("header"))
                    return null;

                int header = json["header"].Value<int>();
                var data = json["data"];

                // Create binary packet
                // Format: [Length:4][Header:2][Data:n]
                using (var ms = new System.IO.MemoryStream())
                using (var writer = new System.IO.BinaryWriter(ms))
                {
                    // Write header (2 bytes, big endian)
                    writer.Write((byte)(header >> 8));
                    writer.Write((byte)(header & 0xFF));

                    // Write data if present
                    if (data != null && data.Type != JTokenType.Null)
                    {
                        WriteJsonData(writer, data);
                    }

                    byte[] packet = ms.ToArray();
                    
                    // Prepend length (4 bytes, big endian)
                    byte[] result = new byte[packet.Length + 4];
                    result[0] = (byte)(packet.Length >> 24);
                    result[1] = (byte)((packet.Length >> 16) & 0xFF);
                    result[2] = (byte)((packet.Length >> 8) & 0xFF);
                    result[3] = (byte)(packet.Length & 0xFF);
                    Array.Copy(packet, 0, result, 4, packet.Length);

                    return result;
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error converting JSON to binary: {ex.Message}");
                return null;
            }
        }

        /// <summary>
        /// Convert binary packet to JSON for Nitro client
        /// </summary>
        private string ConvertBinaryToJson(byte[] data)
        {
            try
            {
                if (data == null || data.Length < 6)
                    return null;

                // Parse binary packet
                // Format: [Length:4][Header:2][Data:n]
                int length = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int header = (data[4] << 8) | data[5];

                var json = new JObject
                {
                    ["header"] = header
                };

                // Parse data if present
                if (data.Length > 6)
                {
                    byte[] packetData = new byte[data.Length - 6];
                    Array.Copy(data, 6, packetData, 0, packetData.Length);
                    
                    json["data"] = ParseBinaryData(packetData);
                }

                return json.ToString(Formatting.None);
            }
            catch (Exception ex)
            {
                log.Error($"Error converting binary to JSON: {ex.Message}");
                return null;
            }
        }

        /// <summary>
        /// Write JSON data to binary writer
        /// </summary>
        private void WriteJsonData(System.IO.BinaryWriter writer, JToken data)
        {
            switch (data.Type)
            {
                case JTokenType.String:
                    WriteString(writer, data.Value<string>());
                    break;
                case JTokenType.Integer:
                    WriteInt(writer, data.Value<int>());
                    break;
                case JTokenType.Boolean:
                    writer.Write(data.Value<bool>() ? (byte)1 : (byte)0);
                    break;
                case JTokenType.Object:
                    foreach (var prop in ((JObject)data).Properties())
                    {
                        WriteJsonData(writer, prop.Value);
                    }
                    break;
                case JTokenType.Array:
                    foreach (var item in (JArray)data)
                    {
                        WriteJsonData(writer, item);
                    }
                    break;
            }
        }

        /// <summary>
        /// Parse binary data to JSON
        /// </summary>
        private JToken ParseBinaryData(byte[] data)
        {
            // Simple implementation - can be extended
            try
            {
                string str = Encoding.UTF8.GetString(data);
                return JToken.Parse(str);
            }
            catch
            {
                // Return as base64 if not valid JSON
                return Convert.ToBase64String(data);
            }
        }

        /// <summary>
        /// Write string to binary (Habbo format: length + data)
        /// </summary>
        private void WriteString(System.IO.BinaryWriter writer, string value)
        {
            byte[] bytes = Encoding.UTF8.GetBytes(value);
            writer.Write((short)bytes.Length);
            writer.Write(bytes);
        }

        /// <summary>
        /// Write integer to binary (Habbo format: 4 bytes big endian)
        /// </summary>
        private void WriteInt(System.IO.BinaryWriter writer, int value)
        {
            writer.Write((byte)(value >> 24));
            writer.Write((byte)((value >> 16) & 0xFF));
            writer.Write((byte)((value >> 8) & 0xFF));
            writer.Write((byte)(value & 0xFF));
        }

        public void Dispose()
        {
            if (_disposed)
                return;

            _disposed = true;
            Disconnect();
        }
    }
}
