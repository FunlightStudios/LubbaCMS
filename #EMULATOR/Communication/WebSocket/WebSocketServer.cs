using System;
using System.Collections.Generic;
using System.Linq;
using Fleck;
using log4net;

namespace Plus.Communication.WebSocket
{
    /// <summary>
    /// WebSocket Server for Nitro Client Support
    /// Runs parallel to TCP Socket for Flash Client
    /// </summary>
    public class WebSocketServer
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.WebSocket");
        
        private Fleck.WebSocketServer _server;
        private readonly Dictionary<IWebSocketConnection, WebSocketConnectionWrapper> _connections;
        private readonly int _port;
        private readonly int _maxConnections;
        private readonly int _connectionsPerIP;
        private bool _isRunning;

        public delegate void WebSocketConnectionEvent(WebSocketConnectionWrapper connection);
        public event WebSocketConnectionEvent OnConnection;
        public event WebSocketConnectionEvent OnDisconnection;

        public WebSocketServer(int port, int maxConnections, int connectionsPerIP)
        {
            _port = port;
            _maxConnections = maxConnections;
            _connectionsPerIP = connectionsPerIP;
            _connections = new Dictionary<IWebSocketConnection, WebSocketConnectionWrapper>();
            _isRunning = false;
        }

        /// <summary>
        /// Start the WebSocket server
        /// </summary>
        public void Start()
        {
            try
            {
                FleckLog.Level = LogLevel.Error; // Reduce Fleck logging
                
                _server = new Fleck.WebSocketServer($"ws://0.0.0.0:{_port}");
                
                _server.Start(socket =>
                {
                    socket.OnOpen = () => HandleConnection(socket);
                    socket.OnClose = () => HandleDisconnection(socket);
                    socket.OnMessage = message => HandleMessage(socket, message);
                    socket.OnBinary = data => HandleBinaryMessage(socket, data);
                    socket.OnError = error => HandleError(socket, error);
                });

                _isRunning = true;
                log.Info($"WebSocket Server started on port {_port} (Nitro Client Support)");
                log.Info($"Maximum connections per IP: {_connectionsPerIP}");
            }
            catch (Exception ex)
            {
                log.Error($"Failed to start WebSocket Server: {ex.Message}");
                throw;
            }
        }

        /// <summary>
        /// Stop the WebSocket server
        /// </summary>
        public void Stop()
        {
            if (!_isRunning)
                return;

            try
            {
                _isRunning = false;
                
                // Close all connections
                var connectionsToClose = _connections.Values.ToList();
                foreach (var connection in connectionsToClose)
                {
                    connection.Disconnect();
                }
                
                _connections.Clear();
                _server?.Dispose();
                
                log.Info("WebSocket Server stopped");
            }
            catch (Exception ex)
            {
                log.Error($"Error stopping WebSocket Server: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle new WebSocket connection
        /// </summary>
        private void HandleConnection(IWebSocketConnection socket)
        {
            try
            {
                string ip = socket.ConnectionInfo.ClientIpAddress;
                
                // Check connection limits
                int currentConnections = GetConnectionCountForIP(ip);
                if (currentConnections >= _connectionsPerIP)
                {
                    log.Warn($"Connection denied from {ip}. Too many connections ({currentConnections})");
                    socket.Close();
                    return;
                }

                if (_connections.Count >= _maxConnections)
                {
                    log.Warn($"Connection denied from {ip}. Server full ({_connections.Count})");
                    socket.Close();
                    return;
                }

                // Create wrapper
                var wrapper = new WebSocketConnectionWrapper(socket, ip);
                _connections.Add(socket, wrapper);
                
                log.Info($"WebSocket connection established from {ip} (ID: {wrapper.ConnectionId})");
                
                // Notify event
                OnConnection?.Invoke(wrapper);
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket connection: {ex.Message}");
                socket.Close();
            }
        }

        /// <summary>
        /// Handle WebSocket disconnection
        /// </summary>
        private void HandleDisconnection(IWebSocketConnection socket)
        {
            try
            {
                if (_connections.TryGetValue(socket, out var wrapper))
                {
                    log.Info($"WebSocket connection closed from {wrapper.IP} (ID: {wrapper.ConnectionId})");
                    
                    _connections.Remove(socket);
                    
                    // Notify event
                    OnDisconnection?.Invoke(wrapper);
                    
                    wrapper.Dispose();
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket disconnection: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle incoming text message (JSON)
        /// </summary>
        private void HandleMessage(IWebSocketConnection socket, string message)
        {
            try
            {
                if (_connections.TryGetValue(socket, out var wrapper))
                {
                    wrapper.HandleIncomingMessage(message);
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket message: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle incoming binary message
        /// </summary>
        private void HandleBinaryMessage(IWebSocketConnection socket, byte[] data)
        {
            try
            {
                if (_connections.TryGetValue(socket, out var wrapper))
                {
                    wrapper.HandleIncomingBinary(data);
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket binary message: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle WebSocket error
        /// </summary>
        private void HandleError(IWebSocketConnection socket, Exception error)
        {
            log.Error($"WebSocket error: {error.Message}");
        }

        /// <summary>
        /// Get connection count for specific IP
        /// </summary>
        private int GetConnectionCountForIP(string ip)
        {
            return _connections.Values.Count(c => c.IP == ip);
        }

        /// <summary>
        /// Get current connection count
        /// </summary>
        public int GetConnectionCount()
        {
            return _connections.Count;
        }

        /// <summary>
        /// Check if server is running
        /// </summary>
        public bool IsRunning => _isRunning;
    }
}
