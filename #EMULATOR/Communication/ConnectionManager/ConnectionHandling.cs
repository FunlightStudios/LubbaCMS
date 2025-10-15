using System;
using Plus.Communication.ConnectionManager;
using Plus.Communication;
using Plus.Communication.WebSocket;
using log4net;

namespace Plus.Communication.ConnectionManager
{
    public class ConnectionHandling
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.ConnectionManager");
        
        private readonly SocketManager manager;
        private WebSocketConnectionHandler webSocketHandler;
        private readonly bool webSocketEnabled;

        public ConnectionHandling(int port, int maxConnections, int connectionsPerIP, bool enabeNagles, bool enableWebSocket = true)
        {
            manager = new SocketManager();
            manager.init(port, maxConnections, connectionsPerIP, new InitialPacketParser(), !enabeNagles);
            webSocketEnabled = enableWebSocket;
            
            if (webSocketEnabled)
            {
                // Initialize WebSocket on separate port
                // TCP: 30000, WebSocket: 2096 (Nitro standard port)
                int wsPort = 2096;
                webSocketHandler = new WebSocketConnectionHandler(wsPort, maxConnections, connectionsPerIP);
                log.Info($"Dual-Protocol Mode: Flash TCP:{port} + Nitro WebSocket:{wsPort}");
            }
            else
            {
                log.Info("Single-Protocol Mode: Flash (TCP) only");
            }
        }

        public void init()
        {
            // Initialize TCP Socket for Flash
            manager.connectionEvent += manager_connectionEvent;
            manager.initializeConnectionRequests();
            
            // Initialize WebSocket for Nitro
            if (webSocketEnabled && webSocketHandler != null)
            {
                try
                {
                    webSocketHandler.Initialize();
                }
                catch (Exception ex)
                {
                    log.Error($"Failed to initialize WebSocket: {ex.Message}");
                    log.Warn("Continuing with Flash-only mode");
                }
            }
        }

        private void manager_connectionEvent(ConnectionInformation connection)
        {
            connection.connectionChanged += connectionChanged;
        }

        private void connectionChanged(ConnectionInformation information, ConnectionState state)
        {
            if (state == ConnectionState.CLOSED)
            {
                CloseConnection(information);
            }
        }

        private void CloseConnection(ConnectionInformation Connection)
        {
            try
            {
                Connection.Dispose();
                PlusEnvironment.GetGame().GetClientManager().DisposeConnection(Convert.ToInt32( Connection.getConnectionID()));
            }
            catch (Exception e)
            {
                Core.ExceptionLogger.LogException(e);
            }
        }

        public void Destroy()
        {
            manager.destroy();
            
            // Destroy WebSocket handler
            if (webSocketHandler != null)
            {
                webSocketHandler.Destroy();
            }
        }
    }
}