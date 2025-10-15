using System;
using System.Collections.Concurrent;
using System.Linq;
using log4net;
using Plus.Communication.WebSocket;
using Plus.Communication.Packets.Outgoing.Inventory;
using Plus.Communication.Packets.Outgoing.Catalog;
using Plus.Communication.Packets.Outgoing.Navigator;
using Plus.Communication.Packets.Outgoing.Rooms;
using Plus.Communication.Packets.Outgoing.Users;
using Plus.HabboHotel.Rooms;
using Newtonsoft.Json.Linq;

namespace Plus.Communication.WebSocket
{
    /// <summary>
    /// Handles WebSocket connections and integrates with GameClient system
    /// </summary>
    public class WebSocketConnectionHandler
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.WebSocket");
        
        private readonly WebSocketServer _server;
        private readonly ConcurrentDictionary<int, NitroClient> _clients;
        private readonly ConcurrentDictionary<int, (int X, int Y)> _userPositions = new ConcurrentDictionary<int, (int, int)>();
        private readonly ConcurrentDictionary<int, System.Threading.CancellationTokenSource> _movementCancellations = new ConcurrentDictionary<int, System.Threading.CancellationTokenSource>();
        private readonly ConcurrentDictionary<int, long> _lastLookTime = new ConcurrentDictionary<int, long>();
        private readonly ConcurrentDictionary<int, int> _currentRooms = new ConcurrentDictionary<int, int>();
        private bool _initialized;

        public WebSocketConnectionHandler(int port, int maxConnections, int connectionsPerIP)
        {
            _server = new WebSocketServer(port, maxConnections, connectionsPerIP);
            _clients = new ConcurrentDictionary<int, NitroClient>();
            _userPositions = new ConcurrentDictionary<int, (int X, int Y)>();
            _movementCancellations = new ConcurrentDictionary<int, System.Threading.CancellationTokenSource>();
            _initialized = false;
        }

        /// <summary>
        /// Initialize and start the WebSocket server
        /// </summary>
        public void Initialize()
        {
            if (_initialized)
                return;

            try
            {
                // Subscribe to connection events
                _server.OnConnection += HandleNewConnection;
                _server.OnDisconnection += HandleDisconnection;

                // Start the server
                _server.Start();

                _initialized = true;
                log.Info("WebSocket Connection Handler initialized successfully");
            }
            catch (Exception ex)
            {
                log.Error($"Failed to initialize WebSocket Connection Handler: {ex.Message}");
                throw;
            }
        }

        /// <summary>
        /// Handle new WebSocket connection
        /// </summary>
        private void HandleNewConnection(WebSocketConnectionWrapper connection)
        {
            try
            {
                log.Info($"New Nitro client connecting from {connection.IP} (ID: {connection.ConnectionId})");

                // Create NitroClient
                var client = new NitroClient(connection);
                _clients.TryAdd(connection.ConnectionId, client);

                // Subscribe to events for data handling
                connection.OnDataReceived += data => HandleDataReceived(client, data);
                connection.OnConnectionClosed += () => HandleConnectionClosed(client);

                // Send handshake packets
                client.SendHandshake();
                
                log.Info($"Nitro client ready, waiting for authentication (ID: {connection.ConnectionId})");
            }
            catch (Exception ex)
            {
                log.Error($"Error handling new WebSocket connection: {ex.Message}");
                connection.Disconnect();
            }
        }

        /// <summary>
        /// Handle WebSocket disconnection
        /// </summary>
        private void HandleDisconnection(WebSocketConnectionWrapper connection)
        {
            try
            {
                log.Info($"Nitro client disconnected from {connection.IP} (ID: {connection.ConnectionId})");
                
                // Remove client
                if (_clients.TryRemove(connection.ConnectionId, out var client))
                {
                    client.Dispose();
                }
                
                // Cancel any active movement
                if (_movementCancellations.TryRemove(connection.ConnectionId, out var cts))
                {
                    cts.Cancel();
                    cts.Dispose();
                }
                
                // Remove position tracking
                _userPositions.TryRemove(connection.ConnectionId, out _);
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket disconnection: {ex.Message}");
            }
        }

        /// <summary>
        /// Handle incoming data from WebSocket
        /// </summary>
        private void HandleDataReceived(NitroClient client, byte[] data)
        {
            try
            {
                // Nitro uses BINARY protocol (same as Flash)
                // Parse binary packet: [Length:4][Header:2][Data:n]
                
                if (data.Length < 6)
                {
                    log.Warn($"Received invalid packet from client {client.ConnectionId} (too short)");
                    return;
                }
                
                // Read packet length (4 bytes, big endian)
                int length = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                // Read packet header (2 bytes, big endian)
                int header = (data[4] << 8) | data[5];
                
                // Read packet data
                byte[] packetData = new byte[data.Length - 6];
                if (packetData.Length > 0)
                {
                    Array.Copy(data, 6, packetData, 0, packetData.Length);
                }
                
                log.Debug($"Received packet {header} ({length} bytes) from Nitro client {client.ConnectionId}");
                
                // Handle packet based on header
                HandleNitroPacket(client, header, packetData);
            }
            catch (Exception ex)
            {
                log.Error($"Error handling WebSocket data: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle Nitro packet by header
        /// </summary>
        private void HandleNitroPacket(NitroClient client, int header, byte[] data)
        {
            try
            {
                switch (header)
                {
                    case 4000: // SSO Ticket (Login) - Nitro uses header 4000
                    case 415:  // SSO Ticket (Alternative header)
                        HandleSSOTicket(client, data);
                        break;
                        
                    case 2596: // Pong
                    case 3928: // Pong (alternative)
                        // Client responded to ping
                        log.Debug($"Received Pong from client {client.ConnectionId}");
                        break;
                        
                    case 3001: // GetUserInfo
                        // Client requests user info (already sent)
                        break;
                        
                    case 3150: // Request Bot Inventory
                        client.SendPacket(NitroBotInventoryComposer.Compose());
                        log.Debug($"Sent Bot Inventory to client {client.ConnectionId}");
                        break;
                        
                    case 2769: // Request Pet Inventory
                        client.SendPacket(NitroPetInventoryComposer.Compose());
                        log.Debug($"Sent Pet Inventory to client {client.ConnectionId}");
                        break;
                        
                    case 3848: // Request Catalog Index
                        client.SendPacket(NitroCatalogIndexComposer.Compose());
                        log.Debug($"Sent Catalog Index to client {client.ConnectionId}");
                        break;
                        
                    case 3878: // Navigator Search
                    case 2690:
                        var navPacket = NitroNavigatorSearchResultsComposer.Compose("", client.GetHabbo());
                        var navBytes = navPacket.GetBytes();
                        
                        // Debug: Log first 100 bytes as hex
                        var hexDump = string.Join(" ", navBytes.Take(100).Select(b => b.ToString("X2")));
                        log.Info($"Navigator packet hex (first 100 bytes): {hexDump}");
                        
                        client.SendPacket(navPacket);
                        log.Info($"Sent Navigator Search Results to client {client.ConnectionId} - Packet size: {navBytes.Length} bytes");
                        break;
                        
                    case 2312: // ROOM_ENTER - Enter Room Request
                        HandleRoomEntry(client, data);
                        break;
                        
                    case 2230: // ROOM_INFO - Room Info Request
                        HandleRoomInfoRequest(client, data);
                        break;
                        
                    case 3898: // Unknown room packet
                        log.Debug($"Received packet 3898 from client {client.ConnectionId}");
                        break;
                        
                    case 3320: // User movement
                        HandleUserMovement(client, data);
                        break;
                    
                    case 1314: // User chat
                        HandleUserChat(client, data);
                        break;
                    
                    case 2091: // User action/emote (expressions)
                        HandleUserAction(client, data);
                        break;
                    
                    case 2080: // User dance
                        HandleUserDance(client, data);
                        break;
                    
                    case 1975: // User sign
                        HandleUserSign(client, data);
                        break;
                    
                    case 3301: // User look at position
                        HandleUserLook(client, data);
                        break;
                    
                    case 2752: // CREATE_FLAT - Create Room (placeholder, check logs for actual header)
                        HandleCreateRoom(client, data);
                        break;
                        
                    case 1597: // UNIT_TYPING - User typing
                    case 1474: // UNIT_TYPING_STOP - User stopped typing
                        // Ignore for now
                        break;
                        
                    case 2300: // Unknown room action
                        log.Debug($"Received packet 2300 from client {client.ConnectionId}");
                        break;
                        
                    default:
                        // Log unhandled packets with hex dump for debugging
                        if (data != null && data.Length > 0)
                        {
                            var hexData = string.Join(" ", data.Take(20).Select(b => b.ToString("X2")));
                            log.Debug($"Unhandled Nitro packet: {header} - Data: {hexData}");
                        }
                        else
                        {
                            log.Debug($"Unhandled Nitro packet: {header}");
                        }
                        break;
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling Nitro packet {header}: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Calculate rotation based on movement direction
        /// </summary>
        private int CalculateRotation(int deltaX, int deltaY)
        {
            // Habbo rotation: 0=North, 1=NE, 2=East, 3=SE, 4=South, 5=SW, 6=West, 7=NW
            if (deltaX == 0 && deltaY < 0) return 0; // North
            if (deltaX > 0 && deltaY < 0) return 1; // NE
            if (deltaX > 0 && deltaY == 0) return 2; // East
            if (deltaX > 0 && deltaY > 0) return 3; // SE
            if (deltaX == 0 && deltaY > 0) return 4; // South
            if (deltaX < 0 && deltaY > 0) return 5; // SW
            if (deltaX < 0 && deltaY == 0) return 6; // West
            if (deltaX < 0 && deltaY < 0) return 7; // NW
            return 2; // Default to East
        }
        
        /// <summary>
        /// Calculate direction from current position to target position
        /// </summary>
        private int CalculateDirection(int fromX, int fromY, int toX, int toY)
        {
            int deltaX = toX - fromX;
            int deltaY = toY - fromY;
            
            // Normalize to -1, 0, or 1
            if (deltaX != 0) deltaX = deltaX / Math.Abs(deltaX);
            if (deltaY != 0) deltaY = deltaY / Math.Abs(deltaY);
            
            return CalculateRotation(deltaX, deltaY);
        }
        
        /// <summary>
        /// Calculate path from start to end with collision detection
        /// </summary>
        private System.Collections.Generic.List<(int X, int Y)> CalculatePath(int startX, int startY, int endX, int endY, Room room = null)
        {
            var path = new System.Collections.Generic.List<(int X, int Y)>();
            
            // Use room's pathfinding system
            var gameMap = room?.GetGameMap();
            
            // If no room or gameMap, use simple pathfinding without collision
            if (room == null || gameMap == null)
            {
                return CalculateSimplePath(startX, startY, endX, endY, null);
            }
            
            // Use custom pathfinding with collision detection
            // We can't use Plus Emulator's PathFinder directly because it requires a RoomUser
            // Instead, we'll use a simple A* implementation that checks tile walkability
            
            try
            {
                var pathResult = FindPathWithCollision(gameMap, startX, startY, endX, endY);
                if (pathResult != null && pathResult.Count > 0)
                {
                    return pathResult;
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error in pathfinding: {ex.Message}");
            }
            
            // Fallback to simple path if pathfinding fails (with collision detection)
            log.Warn($"Pathfinding failed, using simple path with collision");
            return CalculateSimplePath(startX, startY, endX, endY, gameMap);
            
            return path;
        }
        
        /// <summary>
        /// Pathfinding with collision detection using GameMap
        /// </summary>
        private System.Collections.Generic.List<(int X, int Y)> FindPathWithCollision(Gamemap gameMap, int startX, int startY, int endX, int endY)
        {
            var path = new System.Collections.Generic.List<(int X, int Y)>();
            
            // Simple greedy pathfinding with collision checks
            int currentX = startX;
            int currentY = startY;
            
            int maxSteps = 100; // Prevent infinite loops
            int steps = 0;
            
            while ((currentX != endX || currentY != endY) && steps < maxSteps)
            {
                steps++;
                
                int nextX = currentX;
                int nextY = currentY;
                
                // Try to move towards target
                if (currentX < endX)
                    nextX++;
                else if (currentX > endX)
                    nextX--;
                
                if (currentY < endY)
                    nextY++;
                else if (currentY > endY)
                    nextY--;
                
                // Check if the tile is walkable (checks furniture blocking)
                if (IsTileWalkable(gameMap, nextX, nextY))
                {
                    path.Add((nextX, nextY));
                    currentX = nextX;
                    currentY = nextY;
                }
                else
                {
                    // Tile is blocked, try alternative routes
                    bool foundAlternative = false;
                    
                    // Try horizontal first
                    if (currentX != endX)
                    {
                        int altX = currentX + (currentX < endX ? 1 : -1);
                        if (IsTileWalkable(gameMap, altX, currentY))
                        {
                            path.Add((altX, currentY));
                            currentX = altX;
                            foundAlternative = true;
                        }
                    }
                    
                    // Try vertical if horizontal didn't work
                    if (!foundAlternative && currentY != endY)
                    {
                        int altY = currentY + (currentY < endY ? 1 : -1);
                        if (IsTileWalkable(gameMap, currentX, altY))
                        {
                            path.Add((currentX, altY));
                            currentY = altY;
                            foundAlternative = true;
                        }
                    }
                    
                    // If no alternative found, we're stuck
                    if (!foundAlternative)
                    {
                        log.Warn($"Pathfinding stuck at ({currentX}, {currentY}), target ({endX}, {endY})");
                        break;
                    }
                }
            }
            
            return path;
        }
        
        /// <summary>
        /// Check if a tile is walkable (no furniture blocking)
        /// </summary>
        private bool IsTileWalkable(Gamemap gameMap, int x, int y)
        {
            try
            {
                // Check if tile is in bounds and not blocked by model
                if (x < 0 || y < 0 || x >= gameMap.Model.MapSizeX || y >= gameMap.Model.MapSizeY)
                {
                    log.Debug($"Tile ({x}, {y}) out of bounds");
                    return false;
                }
                
                if (gameMap.Model.SqState[x, y] == SquareState.BLOCKED)
                {
                    log.Debug($"Tile ({x}, {y}) blocked by model");
                    return false;
                }
                
                // Check if there's furniture on this tile
                var items = gameMap.GetCoordinatedItems(new System.Drawing.Point(x, y));
                if (items == null || items.Count == 0)
                {
                    log.Debug($"Tile ({x}, {y}) is walkable (no furniture)");
                    return true;
                }
                
                // Check if any furniture blocks walking
                foreach (var item in items)
                {
                    if (item != null && item.GetBaseItem() != null && !item.GetBaseItem().Walkable)
                    {
                        log.Debug($"Tile ({x}, {y}) blocked by furniture: {item.GetBaseItem().ItemName}");
                        return false;
                    }
                }
                
                log.Debug($"Tile ({x}, {y}) is walkable (has walkable furniture)");
                return true;
            }
            catch (Exception ex)
            {
                log.Error($"Error checking tile walkability at ({x}, {y}): {ex.Message}");
                return false;
            }
        }
        
        /// <summary>
        /// Simple pathfinding with basic collision detection
        /// </summary>
        private System.Collections.Generic.List<(int X, int Y)> CalculateSimplePath(int startX, int startY, int endX, int endY, Gamemap gameMap = null)
        {
            var path = new System.Collections.Generic.List<(int X, int Y)>();
            
            int currentX = startX;
            int currentY = startY;
            
            // Simple pathfinding: move one tile at a time towards target
            while (currentX != endX || currentY != endY)
            {
                int nextX = currentX;
                int nextY = currentY;
                
                // Move horizontally
                if (currentX < endX)
                    nextX++;
                else if (currentX > endX)
                    nextX--;
                
                // Move vertically
                if (currentY < endY)
                    nextY++;
                else if (currentY > endY)
                    nextY--;
                
                // Check if next tile is walkable (if gameMap provided)
                if (gameMap != null && !IsTileWalkable(gameMap, nextX, nextY))
                {
                    log.Warn($"Simple path blocked at ({nextX}, {nextY}), stopping");
                    break; // Stop at last walkable tile
                }
                
                path.Add((nextX, nextY));
                currentX = nextX;
                currentY = nextY;
            }
            
            return path;
        }
        
        /// <summary>
        /// Handle user movement
        /// </summary>
        private void HandleUserMovement(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 8)
                {
                    log.Warn($"Invalid movement packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read coordinates (X, Y as integers)
                int targetX = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int targetY = (data[4] << 24) | (data[5] << 16) | (data[6] << 8) | data[7];
                
                log.Info($"Nitro client {client.ConnectionId} moving to ({targetX}, {targetY})");
                
                // Cancel any existing movement for this client
                if (_movementCancellations.TryRemove(client.ConnectionId, out var oldCts))
                {
                    oldCts.Cancel();
                    oldCts.Dispose();
                }
                
                // Get current position (default to door if not tracked)
                if (!_userPositions.TryGetValue(client.ConnectionId, out var currentPos))
                {
                    currentPos = (0, 10); // Default door position
                    _userPositions[client.ConnectionId] = currentPos;
                }
                
                // Get room for collision detection
                Room room = null;
                Gamemap gameMap = null;
                if (_currentRooms.TryGetValue(client.ConnectionId, out int currentRoomId))
                {
                    room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(currentRoomId);
                    gameMap = room?.GetGameMap();
                }
                
                // Check if target tile is walkable (prevent clicking on blocked tiles)
                if (gameMap != null && !IsTileWalkable(gameMap, targetX, targetY))
                {
                    log.Warn($"Target tile ({targetX}, {targetY}) is not walkable, ignoring movement");
                    return;
                }
                
                // Calculate path from current position to target (with collision if room available)
                var path = CalculatePath(currentPos.X, currentPos.Y, targetX, targetY, room);
                
                if (path.Count == 0)
                {
                    log.Warn($"No path found for client {client.ConnectionId} from ({currentPos.X}, {currentPos.Y}) to ({targetX}, {targetY})");
                    return;
                }
                
                // Create new cancellation token for this movement
                var cts = new System.Threading.CancellationTokenSource();
                _movementCancellations[client.ConnectionId] = cts;
                
                // Walk tile-by-tile along the path
                int delay = 0;
                for (int i = 0; i < path.Count; i++)
                {
                    var step = path[i];
                    var prevPos = i == 0 ? currentPos : path[i - 1];
                    
                    // Calculate direction for this step
                    int deltaX = step.X - prevPos.X;
                    int deltaY = step.Y - prevPos.Y;
                    int rotation = CalculateRotation(deltaX, deltaY);
                    
                    // Determine if this is the last step
                    bool isLastStep = (i == path.Count - 1);
                    
                    // Get Z height for this step (model height + furniture height)
                    double stepZ = 0.0;
                    double prevZ = 0.0;
                    if (gameMap != null && gameMap.Model != null)
                    {
                        // Get base model height
                        double modelHeight = gameMap.Model.SqFloorHeight[step.X, step.Y];
                        double prevModelHeight = gameMap.Model.SqFloorHeight[prevPos.X, prevPos.Y];
                        
                        // Get furniture height (if any)
                        double furnitureHeight = gameMap.GetHeightForSquare(new System.Drawing.Point(step.X, step.Y));
                        double prevFurnitureHeight = gameMap.GetHeightForSquare(new System.Drawing.Point(prevPos.X, prevPos.Y));
                        
                        // Combine: use furniture height if > 0, otherwise use model height
                        stepZ = furnitureHeight > 0 ? furnitureHeight : modelHeight;
                        prevZ = prevFurnitureHeight > 0 ? prevFurnitureHeight : prevModelHeight;
                    }
                    
                    // Capture variables for closure
                    var capturedStep = step;
                    var capturedPrevPos = prevPos;
                    var capturedRotation = rotation;
                    var capturedIsLast = isLastStep;
                    var capturedStepZ = stepZ;
                    var capturedPrevZ = prevZ;
                    
                    // Schedule this step
                    System.Threading.Tasks.Task.Delay(delay, cts.Token).ContinueWith(_ =>
                    {
                        if (cts.Token.IsCancellationRequested)
                            return;
                        
                        try
                        {
                            // Send movement to next tile (same for all steps)
                            string actions = $"mv {capturedStep.X},{capturedStep.Y},{capturedStepZ:F2}/";
                            client.SendPacket(NitroUnitStatusComposer.Compose(1, capturedPrevPos.X, capturedPrevPos.Y, capturedPrevZ, capturedRotation, capturedRotation, actions));
                            
                            // Update position
                            _userPositions[client.ConnectionId] = capturedStep;
                            
                            if (capturedIsLast)
                            {
                                // Last step: send STOP after animation completes
                                System.Threading.Tasks.Task.Delay(500, cts.Token).ContinueWith(__ =>
                                {
                                    if (cts.Token.IsCancellationRequested)
                                        return;
                                    
                                    try
                                    {
                                        client.SendPacket(NitroUnitStatusComposer.Compose(1, capturedStep.X, capturedStep.Y, capturedStepZ, capturedRotation, capturedRotation, "/"));
                                        log.Info($"Client {client.ConnectionId} arrived at ({capturedStep.X}, {capturedStep.Y}, Z={capturedStepZ:F2})");
                                        
                                        // Remove cancellation token
                                        _movementCancellations.TryRemove(client.ConnectionId, out System.Threading.CancellationTokenSource _);
                                    }
                                    catch (Exception ex)
                                    {
                                        log.Error($"Error sending stop: {ex.Message}");
                                    }
                                }, System.Threading.Tasks.TaskContinuationOptions.OnlyOnRanToCompletion);
                            }
                        }
                        catch (Exception ex)
                        {
                            log.Error($"Error sending movement step: {ex.Message}");
                        }
                    }, System.Threading.Tasks.TaskContinuationOptions.OnlyOnRanToCompletion);
                    
                    // Increment delay for next step (500ms per tile)
                    delay += 500;
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user movement: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle user action/emote
        /// </summary>
        private void HandleUserAction(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid action packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read action ID (4 bytes, big endian)
                int actionId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                log.Info($"Nitro client {client.ConnectionId} performing action: {actionId}");
                
                // Check if this is an automatic wave from clicking on avatar
                // Only filter Wave (actionId 1) that comes immediately after a look
                if (actionId == 1 && _lastLookTime.TryGetValue(client.ConnectionId, out long lastLookTime))
                {
                    long currentTime = DateTimeOffset.UtcNow.ToUnixTimeMilliseconds();
                    long timeSinceLook = currentTime - lastLookTime;
                    
                    // If wave comes within 200ms of a look action, it's automatic - ignore it
                    if (timeSinceLook < 200)
                    {
                        log.Info($"Ignoring automatic wave from client {client.ConnectionId} (came {timeSinceLook}ms after look)");
                        return;
                    }
                }
                
                // Get current position
                if (!_userPositions.TryGetValue(client.ConnectionId, out var currentPos))
                {
                    currentPos = (0, 10); // Default door position
                }
                
                // Send expression packet (separate from status)
                // Expression IDs: 1=wave, 2=blow, 3=laugh, 4=cry, 5=idle, 6=dance, 7=respect
                client.SendPacket(NitroUnitExpressionComposer.Compose(1, actionId));
                log.Info($"Sent expression {actionId} for client {client.ConnectionId}");
                
                // Expressions auto-clear after their animation duration
                // No need to manually clear them
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user action: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle user dance
        /// </summary>
        private void HandleUserDance(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid dance packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read dance ID (4 bytes, big endian)
                int danceId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                log.Info($"Nitro client {client.ConnectionId} dancing: {danceId}");
                
                // Send dance packet (0 = stop, 1-4 = dance styles)
                client.SendPacket(NitroUnitDanceComposer.Compose(1, danceId));
                log.Info($"Sent dance {danceId} for client {client.ConnectionId}");
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user dance: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle user sign (hand signals 0-17)
        /// </summary>
        private void HandleUserSign(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid sign packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read sign ID (4 bytes, big endian)
                int signId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                log.Info($"Nitro client {client.ConnectionId} showing sign: {signId}");
                
                // Get current position
                if (!_userPositions.TryGetValue(client.ConnectionId, out var currentPos))
                {
                    currentPos = (0, 10); // Default door position
                }
                
                // Get room and calculate Z height
                double currentZ = 0.0;
                Room room = null;
                if (_currentRooms.TryGetValue(client.ConnectionId, out int currentRoomId))
                {
                    room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(currentRoomId);
                    var gameMap = room?.GetGameMap();
                    
                    if (gameMap != null && gameMap.Model != null)
                    {
                        // Get base model height
                        double modelHeight = gameMap.Model.SqFloorHeight[currentPos.X, currentPos.Y];
                        
                        // Get furniture height (if any)
                        double furnitureHeight = gameMap.GetHeightForSquare(new System.Drawing.Point(currentPos.X, currentPos.Y));
                        
                        // Combine: use furniture height if > 0, otherwise use model height
                        currentZ = furnitureHeight > 0 ? furnitureHeight : modelHeight;
                    }
                }
                
                // Send sign via status update (signs are in the status string)
                // Format: "std 0/sign X/" where X is the sign number
                string signAction = $"std 0/sign {signId}/";
                client.SendPacket(NitroUnitStatusComposer.Compose(1, currentPos.X, currentPos.Y, currentZ, 2, 2, signAction));
                log.Info($"Sent sign {signId} for client {client.ConnectionId} at Z={currentZ:F2}");
                
                // Capture Z for closure
                var capturedZ = currentZ;
                
                // Auto-clear sign after 2 seconds
                System.Threading.Tasks.Task.Delay(2000).ContinueWith(_ =>
                {
                    try
                    {
                        if (_userPositions.TryGetValue(client.ConnectionId, out var pos))
                        {
                            client.SendPacket(NitroUnitStatusComposer.Compose(1, pos.X, pos.Y, capturedZ, 2, 2, "std 0/"));
                            log.Info($"Cleared sign for client {client.ConnectionId}");
                        }
                    }
                    catch (Exception ex)
                    {
                        log.Error($"Error clearing sign: {ex.Message}");
                    }
                });
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user sign: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle user look at position (when clicking on avatar or tile)
        /// </summary>
        private void HandleUserLook(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 8)
                {
                    log.Warn($"Invalid look packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read X and Y coordinates (4 bytes each, big endian)
                int x = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int y = (data[4] << 24) | (data[5] << 16) | (data[6] << 8) | data[7];
                
                log.Debug($"Nitro client {client.ConnectionId} looking at ({x}, {y})");
                
                // Get current position
                if (!_userPositions.TryGetValue(client.ConnectionId, out var currentPos))
                {
                    currentPos = (0, 10); // Default door position
                }
                
                // Calculate head direction based on target position
                int headDirection = CalculateDirection(currentPos.X, currentPos.Y, x, y);
                
                // Update head direction (send status update with current position but new head direction)
                // For now, we just log it - full implementation would update the head rotation
                log.Debug($"Client {client.ConnectionId} head direction: {headDirection}");
                
                // Store the time of this look action to filter out automatic waves
                _lastLookTime[client.ConnectionId] = DateTimeOffset.UtcNow.ToUnixTimeMilliseconds();
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user look: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle user chat
        /// </summary>
        private void HandleUserChat(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 2)
                {
                    log.Warn($"Invalid chat packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read message length (2 bytes, big endian)
                int messageLength = (data[0] << 8) | data[1];
                
                if (data.Length < 2 + messageLength)
                {
                    log.Warn($"Invalid chat packet length from client {client.ConnectionId}");
                    return;
                }
                
                // Read message
                string message = System.Text.Encoding.UTF8.GetString(data, 2, messageLength);
                
                log.Info($"Nitro client {client.ConnectionId} chat: {message}");
                
                // Check if message is a command
                if (message.StartsWith(":"))
                {
                    // Handle commands directly for Nitro clients
                    HandleNitroCommand(client, message);
                    return; // Don't send the command as chat
                }
                
                // Send chat message back to client
                client.SendPacket(NitroUnitChatComposer.Compose(1, message));
            }
            catch (Exception ex)
            {
                log.Error($"Error handling user chat: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle Nitro commands (simplified command system for Nitro clients)
        /// </summary>
        private void HandleNitroCommand(NitroClient client, string message)
        {
            try
            {
                // Remove the : prefix
                string command = message.Substring(1).ToLower().Trim();
                string[] parts = command.Split(' ');
                string cmd = parts[0];

                log.Info($"Nitro command: {cmd}");

                switch (cmd)
                {
                    case "commands":
                        client.SendPacket(NitroUnitChatComposer.Compose(1, "Available commands: :about, :commands, :pickall, :sit, :stand"));
                        break;

                    case "about":
                    case "info":
                        TimeSpan uptime = DateTime.Now - PlusEnvironment.ServerStarted;
                        int onlineUsers = PlusEnvironment.GetGame().GetClientManager().Count;
                        int roomCount = PlusEnvironment.GetGame().GetRoomManager().Count;
                        
                        string aboutMsg = $"Lubba Emulator | Online: {onlineUsers} | Rooms: {roomCount} | Uptime: {uptime.Days}d {uptime.Hours}h {uptime.Minutes}m";
                        client.SendPacket(NitroUnitChatComposer.Compose(1, aboutMsg));
                        break;

                    case "pickall":
                        client.SendPacket(NitroUnitChatComposer.Compose(1, "Pickall command not yet implemented for Nitro"));
                        break;

                    case "sit":
                        client.SendPacket(NitroUnitChatComposer.Compose(1, "Sit command not yet implemented for Nitro"));
                        break;

                    case "stand":
                        client.SendPacket(NitroUnitChatComposer.Compose(1, "Stand command not yet implemented for Nitro"));
                        break;

                    default:
                        client.SendPacket(NitroUnitChatComposer.Compose(1, $"Unknown command: {cmd}. Type :commands for help"));
                        break;
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling Nitro command: {ex.Message}");
                client.SendPacket(NitroUnitChatComposer.Compose(1, "Error executing command"));
            }
        }

        /// <summary>
        /// Handle create room request
        /// </summary>
        private void HandleCreateRoom(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid create room packet from client {client.ConnectionId}");
                    return;
                }
                
                log.Info($"Nitro client {client.ConnectionId} attempting to create room");
                
                // Parse the packet data
                int offset = 0;
                
                // Read room name (2-byte length + string)
                if (offset + 2 > data.Length) return;
                int nameLength = (data[offset] << 8) | data[offset + 1];
                offset += 2;
                
                if (offset + nameLength > data.Length) return;
                string roomName = System.Text.Encoding.UTF8.GetString(data, offset, nameLength);
                offset += nameLength;
                
                // Read description (2-byte length + string)
                if (offset + 2 > data.Length) return;
                int descLength = (data[offset] << 8) | data[offset + 1];
                offset += 2;
                
                if (offset + descLength > data.Length) return;
                string description = System.Text.Encoding.UTF8.GetString(data, offset, descLength);
                offset += descLength;
                
                // Read model name (2-byte length + string)
                if (offset + 2 > data.Length) return;
                int modelLength = (data[offset] << 8) | data[offset + 1];
                offset += 2;
                
                if (offset + modelLength > data.Length) return;
                string modelName = System.Text.Encoding.UTF8.GetString(data, offset, modelLength);
                offset += modelLength;
                
                // Read category (4 bytes)
                if (offset + 4 > data.Length) return;
                int category = (data[offset] << 24) | (data[offset + 1] << 16) | (data[offset + 2] << 8) | data[offset + 3];
                offset += 4;
                
                // Read max visitors (4 bytes)
                if (offset + 4 > data.Length) return;
                int maxVisitors = (data[offset] << 24) | (data[offset + 1] << 16) | (data[offset + 2] << 8) | data[offset + 3];
                offset += 4;
                
                // Read trade settings (4 bytes)
                if (offset + 4 > data.Length) return;
                int tradeSettings = (data[offset] << 24) | (data[offset + 1] << 16) | (data[offset + 2] << 8) | data[offset + 3];
                
                log.Info($"Create room request: Name='{roomName}', Desc='{description}', Model='{modelName}', Category={category}, MaxVisitors={maxVisitors}, Trade={tradeSettings}");
                
                // Validate and create room using Plus Emulator's logic
                var habbo = client.GetHabbo();
                if (habbo == null)
                {
                    log.Warn($"Client {client.ConnectionId} not authenticated, cannot create room");
                    return;
                }
                
                // Use the existing CreateFlatEvent logic
                if (habbo.UsersRooms.Count >= 500)
                {
                    client.SendPacket(NitroCanCreateRoomComposer.Compose(true, 500));
                    return;
                }
                
                if (roomName.Length < 3 || roomName.Length > 25)
                {
                    log.Warn($"Invalid room name length: {roomName.Length}");
                    return;
                }
                
                HabboHotel.Rooms.RoomModel roomModel = null;
                if (!PlusEnvironment.GetGame().GetRoomManager().TryGetModel(modelName, out roomModel))
                {
                    log.Warn($"Invalid room model: {modelName}");
                    return;
                }
                
                if (maxVisitors < 10 || maxVisitors > 25)
                    maxVisitors = 10;
                
                if (tradeSettings < 0 || tradeSettings > 2)
                    tradeSettings = 0;
                
                // Create the room directly in database (bypassing GameClient requirement)
                int roomId = 0;
                
                using (var dbClient = PlusEnvironment.GetDatabaseManager().GetQueryReactor())
                {
                    dbClient.SetQuery("INSERT INTO `rooms` (`roomtype`,`caption`,`description`,`owner`,`model_name`,`category`,`users_max`,`trade_settings`,`wallpaper`,`floor`,`landscape`,`floorthick`,`wallthick`) VALUES ('private',@caption,@description,@UserId,@model,@category,@usersmax,@tradesettings,@wallpaper,@floor,@landscape,@floorthick,@wallthick)");
                    dbClient.AddParameter("caption", roomName);
                    dbClient.AddParameter("description", description);
                    dbClient.AddParameter("UserId", habbo.Id);
                    dbClient.AddParameter("model", modelName);
                    dbClient.AddParameter("category", category);
                    dbClient.AddParameter("usersmax", maxVisitors);
                    dbClient.AddParameter("tradesettings", tradeSettings);
                    dbClient.AddParameter("wallpaper", "0.0");
                    dbClient.AddParameter("floor", "0.0");
                    dbClient.AddParameter("landscape", "0.0");
                    dbClient.AddParameter("floorthick", 0);
                    dbClient.AddParameter("wallthick", 0);
                    
                    roomId = Convert.ToInt32(dbClient.InsertQuery());
                }
                
                if (roomId > 0)
                {
                    // Generate room data and add to user's rooms
                    var newRoomData = PlusEnvironment.GetGame().GetRoomManager().GenerateRoomData(roomId);
                    if (newRoomData != null)
                    {
                        habbo.UsersRooms.Add(newRoomData);
                        
                        // Send success response
                        client.SendPacket(NitroFlatCreatedComposer.Compose(roomId, roomName));
                        log.Info($"Room created successfully: ID={roomId}, Name='{roomName}'");
                        
                        // Update navigator to show the new room
                        System.Threading.Tasks.Task.Delay(100).ContinueWith(_ =>
                        {
                            try
                            {
                                // Send updated navigator search results with user context
                                client.SendPacket(NitroNavigatorSearchResultsComposer.Compose("", habbo));
                                log.Info($"Sent updated Navigator for client {client.ConnectionId}");
                            }
                            catch (Exception ex)
                            {
                                log.Error($"Error updating navigator: {ex.Message}");
                            }
                        });
                        
                        // DON'T auto-enter - let the client decide
                        // The FlatCreated packet should close the dialog and the user can click to enter
                        // If we want auto-enter, we need to simulate a room click
                    }
                    else
                    {
                        log.Error($"Failed to generate room data for room {roomId}");
                    }
                }
                else
                {
                    log.Error($"Failed to create room in database for client {client.ConnectionId}");
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling create room: {ex.Message}\n{ex.StackTrace}");
            }
        }
        
        /// <summary>
        /// Handle room info request
        /// </summary>
        private void HandleRoomInfoRequest(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid room info request from client {client.ConnectionId}");
                    return;
                }
                
                // Read room ID (4 bytes, big endian)
                int roomId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                log.Info($"Nitro client {client.ConnectionId} requesting room info for room {roomId}");
                
                // Get room
                var room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(roomId);
                
                if (room != null)
                {
                    // Track which room the client is in (for pathfinding)
                    _currentRooms[client.ConnectionId] = roomId;
                    log.Info($"Client {client.ConnectionId} is now in room {roomId}");
                    
                    // Send room info
                    client.SendPacket(NitroRoomInfoComposer.Compose(room));
                    log.Info($"Sent RoomInfo for room {roomId} to client {client.ConnectionId}");
                    
                    // Send RoomModel FIRST (client needs this to initialize the room)
                    client.SendPacket(NitroRoomModelComposer.Compose(room));
                    log.Info($"Sent RoomModel (with furniture collision) for room {roomId} to client {client.ConnectionId}");
                    
                    // Send room door position
                    client.SendPacket(NitroRoomDoorComposer.Compose(room));
                    log.Info($"Sent RoomDoor for room {roomId} to client {client.ConnectionId}");
                    
                    // Send room visualization settings
                    client.SendPacket(NitroRoomPaintComposer.Compose());
                    log.Info($"Sent RoomPaint for room {roomId} to client {client.ConnectionId}");
                    
                    client.SendPacket(NitroRoomThicknessComposer.Compose());
                    log.Info($"Sent RoomThickness for room {roomId} to client {client.ConnectionId}");
                    
                    // Send the user's own avatar in the room BEFORE RoomEnter
                    if (client.GetHabbo() != null)
                    {
                        var gameMap = room.GetGameMap();
                        var doorX = gameMap?.Model?.DoorX ?? 0;
                        var doorY = gameMap?.Model?.DoorY ?? 0;
                        
                        // Get door Z height (model height + furniture height)
                        double doorZ = 0.0;
                        if (gameMap != null && gameMap.Model != null)
                        {
                            // Get base model height
                            double modelHeight = gameMap.Model.SqFloorHeight[doorX, doorY];
                            
                            // Get furniture height (if any)
                            double furnitureHeight = gameMap.GetHeightForSquare(new System.Drawing.Point(doorX, doorY));
                            
                            // Combine: use furniture height if > 0, otherwise use model height
                            doorZ = furnitureHeight > 0 ? furnitureHeight : modelHeight;
                        }
                        
                        // Set initial position to door
                        _userPositions[client.ConnectionId] = (doorX, doorY);
                        
                        client.SendPacket(NitroRoomUsersComposer.ComposeUser(client.GetHabbo(), 1, doorX, doorY, doorZ));
                        log.Info($"Sent User spawn for client {client.ConnectionId} at door position ({doorX}, {doorY}, Z={doorZ:F2}) in room {roomId}");
                        
                        // Send user's current badges (worn on avatar)
                        client.SendPacket(NitroUserCurrentBadgesComposer.Compose(client.GetHabbo()));
                        log.Info($"Sent current badges for client {client.ConnectionId}");
                    }
                    
                    // Send floor items (furniture)
                    try
                    {
                        log.Debug($"About to send floor items for room {roomId}");
                        var floorItemsPacket = NitroRoomFloorItemsComposer.Compose(room);
                        client.SendPacket(floorItemsPacket);
                        log.Info($"Sent floor items for room {roomId} to client {client.ConnectionId}");
                    }
                    catch (Exception ex)
                    {
                        log.Error($"Error sending floor items: {ex.Message}\n{ex.StackTrace}");
                    }
                    
                    // Send the detailed heightmap (redundant but some clients may need it)
                    client.SendPacket(NitroHeightMapComposer.Compose(room));
                    log.Info($"Sent HeightMap for room {roomId} to client {client.ConnectionId}");
                    
                    // Send room entry confirmation LAST
                    client.SendPacket(NitroRoomEntryComposer.Compose(roomId));
                    log.Info($"Sent RoomEnter confirmation for room {roomId} to client {client.ConnectionId}");
                }
                else
                {
                    log.Warn($"Room {roomId} not found for info request from client {client.ConnectionId}");
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling room info request: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle room entry request
        /// </summary>
        private void HandleRoomEntry(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4)
                {
                    log.Warn($"Invalid room entry packet from client {client.ConnectionId}");
                    return;
                }
                
                // Read room ID (4 bytes, big endian)
                int roomId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                
                log.Info($"Nitro client {client.ConnectionId} requesting to enter room {roomId}");
                
                // Get room
                var room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(roomId);
                
                if (room != null)
                {
                    // Validate room has required data
                    if (room.GetGameMap() == null || room.GetGameMap().Model == null)
                    {
                        log.Error($"Room {roomId} has no GameMap or Model! Cannot enter.");
                        return;
                    }
                    
                    // Send room forward (redirects client to room)
                    client.SendPacket(NitroRoomForwardComposer.Compose(roomId));
                    log.Info($"Sent RoomForward for room {roomId} to client {client.ConnectionId}");
                    
                    // Send room model name
                    client.SendPacket(NitroRoomModelNameComposer.Compose(room.ModelName, roomId));
                    log.Info($"Sent RoomModelName ({room.ModelName}) to client {client.ConnectionId}");
                    
                    log.Info($"Nitro client {client.ConnectionId} entering room {roomId}");
                }
                else
                {
                    log.Warn($"Room {roomId} not found for client {client.ConnectionId}");
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling room entry: {ex.Message}");
            }
        }
        
        /// <summary>
        /// Handle SSO Ticket authentication
        /// </summary>
        private void HandleSSOTicket(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 2)
                {
                    log.Warn($"SSO packet without ticket from client {client.ConnectionId}");
                    client.Disconnect();
                    return;
                }
                
                // Read string from binary data
                // Format: [Length:2][String:n]
                int length = (data[0] << 8) | data[1];
                
                if (data.Length < 2 + length)
                {
                    log.Warn($"Invalid SSO packet from client {client.ConnectionId}");
                    client.Disconnect();
                    return;
                }
                
                string ssoTicket = System.Text.Encoding.UTF8.GetString(data, 2, length);
                
                log.Info($"Nitro client {client.ConnectionId} attempting authentication with SSO: {ssoTicket}");
                
                if (client.TryAuthenticate(ssoTicket))
                {
                    log.Info($"Nitro client {client.ConnectionId} successfully authenticated!");
                }
                else
                {
                    log.Warn($"Nitro client {client.ConnectionId} authentication failed");
                    client.Disconnect();
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling SSO ticket: {ex.Message}");
                client.Disconnect();
            }
        }

        /// <summary>
        /// Handle connection closed event
        /// </summary>
        private void HandleConnectionClosed(NitroClient client)
        {
            try
            {
                log.Info($"Nitro client connection closed: {client.ConnectionId}");
                
                // Remove from clients list
                _clients.TryRemove(client.ConnectionId, out _);
                
                client.Dispose();
            }
            catch (Exception ex)
            {
                log.Error($"Error handling connection closed: {ex.Message}");
            }
        }

        /// <summary>
        /// Stop the WebSocket server
        /// </summary>
        public void Destroy()
        {
            try
            {
                if (_server != null && _server.IsRunning)
                {
                    _server.Stop();
                    log.Info("WebSocket Connection Handler destroyed");
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error destroying WebSocket Connection Handler: {ex.Message}");
            }
        }

        /// <summary>
        /// Get current connection count
        /// </summary>
        public int GetConnectionCount()
        {
            return _server?.GetConnectionCount() ?? 0;
        }
    }

    /// <summary>
    /// Wrapper to make WebSocket connection compatible with existing ConnectionInformation
    /// Uses composition instead of inheritance since ConnectionInformation methods are not virtual
    /// </summary>
    public class WebSocketConnectionInformation
    {
        private readonly WebSocketConnectionWrapper _wrapper;
        private readonly ConnectionManager.ConnectionInformation _baseConnection;

        public WebSocketConnectionInformation(WebSocketConnectionWrapper wrapper)
        {
            _wrapper = wrapper;
            // Create a dummy ConnectionInformation for compatibility
            // We'll handle the actual connection through the wrapper
        }

        public void SendData(byte[] data)
        {
            _wrapper.SendData(data);
        }

        public void Dispose()
        {
            _wrapper.Dispose();
        }

        public string getIp()
        {
            return _wrapper.IP;
        }

        public int getConnectionID()
        {
            return _wrapper.ConnectionId;
        }

        public WebSocketConnectionWrapper GetWrapper()
        {
            return _wrapper;
        }
    }
}
