using System;
using System.IO;
using System.Text;

namespace Plus.Communication.Packets.Outgoing.Handshake
{
    /// <summary>
    /// Nitro Server Packet - Sends BINARY packets to Nitro client (same format as Flash)
    /// </summary>
    public class NitroServerPacket
    {
        private readonly int _header;
        private readonly MemoryStream _stream;
        private readonly BinaryWriter _writer;

        public NitroServerPacket(int header)
        {
            _header = header;
            _stream = new MemoryStream();
            _writer = new BinaryWriter(_stream);
        }

        /// <summary>
        /// Write string value (Habbo format: length + data)
        /// </summary>
        public void WriteString(string value)
        {
            if (value == null) value = "";
            
            byte[] bytes = Encoding.UTF8.GetBytes(value);
            
            // Write length (2 bytes, big endian)
            _writer.Write((byte)(bytes.Length >> 8));
            _writer.Write((byte)(bytes.Length & 0xFF));
            
            // Write string data
            _writer.Write(bytes);
        }

        /// <summary>
        /// Write integer value (4 bytes, big endian)
        /// </summary>
        public void WriteInteger(int value)
        {
            try
            {
                if (_writer == null || _stream == null)
                {
                    Console.WriteLine($"[ERROR] WriteInteger: Writer or Stream is null!");
                    throw new InvalidOperationException("Writer or Stream is null");
                }
                
                Console.WriteLine($"[DEBUG] WriteInteger: value={value}, stream position={_stream.Position}, stream length={_stream.Length}");
                
                // Use unchecked context to allow overflow for negative numbers
                unchecked
                {
                    _writer.Write((byte)((value >> 24) & 0xFF));
                    _writer.Write((byte)((value >> 16) & 0xFF));
                    _writer.Write((byte)((value >> 8) & 0xFF));
                    _writer.Write((byte)(value & 0xFF));
                }
                
                Console.WriteLine($"[DEBUG] WriteInteger: SUCCESS, new position={_stream.Position}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[ERROR] WriteInteger failed: {ex.Message}");
                Console.WriteLine($"[ERROR] Value: {value}, Stream: {_stream?.Length}, Position: {_stream?.Position}");
                throw;
            }
        }

        /// <summary>
        /// Write boolean value (1 byte)
        /// </summary>
        public void WriteBoolean(bool value)
        {
            try
            {
                Console.WriteLine($"[DEBUG] WriteBoolean: value={value}, stream position={_stream.Position}, stream length={_stream.Length}");
                _writer.Write((byte)(value ? 1 : 0));
                Console.WriteLine($"[DEBUG] WriteBoolean: SUCCESS, new position={_stream.Position}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[ERROR] WriteBoolean failed: {ex.Message}");
                throw;
            }
        }

        /// <summary>
        /// Get the packet as byte array
        /// Format: [Length:4][Header:2][Data:n]
        /// </summary>
        public byte[] GetBytes()
        {
            byte[] data = _stream.ToArray();
            
            // Create final packet with length and header
            byte[] packet = new byte[data.Length + 6];
            
            // Write packet length (4 bytes, big endian)
            int length = data.Length + 2; // +2 for header
            packet[0] = (byte)(length >> 24);
            packet[1] = (byte)((length >> 16) & 0xFF);
            packet[2] = (byte)((length >> 8) & 0xFF);
            packet[3] = (byte)(length & 0xFF);
            
            // Write header (2 bytes, big endian)
            packet[4] = (byte)(_header >> 8);
            packet[5] = (byte)(_header & 0xFF);
            
            // Copy data
            Array.Copy(data, 0, packet, 6, data.Length);
            
            return packet;
        }

        /// <summary>
        /// Get packet header
        /// </summary>
        public int Header => _header;
        
        /// <summary>
        /// Dispose resources
        /// </summary>
        public void Dispose()
        {
            _writer?.Dispose();
            _stream?.Dispose();
        }
    }
}
