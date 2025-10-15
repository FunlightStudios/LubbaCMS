using System;
using System.Text;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room model (floor plan) to client
    /// </summary>
    public class NitroRoomModelComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1301); // ROOM_MODEL header
            
            try
            {
                Console.WriteLine($"[DEBUG] NitroRoomModelComposer.Compose called for room {room?.Id}");
                
                if (room != null && room.GetGameMap() != null)
                {
                    var model = room.GetGameMap().Model;
                    
                    Console.WriteLine($"[DEBUG] Room {room.Id} - Writing scale...");
                    // Scale (true = 32, false = 64)
                    packet.WriteBoolean(true); // 32 scale
                    
                    Console.WriteLine($"[DEBUG] Room {room.Id} - Writing wall height...");
                    // Wall height (-1 = default)
                    packet.WriteInteger(-1);
                    
                    Console.WriteLine($"[DEBUG] Room {room.Id} - Getting heightmap...");
                    
                    // Model string (heightmap) - BUILD DYNAMIC HEIGHTMAP WITH FURNITURE!
                    var gameMap = room.GetGameMap();
                    string heightMap = "";
                    for (int y = 0; y < model.MapSizeY; y++)
                    {
                        for (int x = 0; x < model.MapSizeX; x++)
                        {
                            // Check if tile is blocked by model
                            if (model.SqState[x, y] == SquareState.BLOCKED)
                            {
                                heightMap += "x";
                            }
                            else
                            {
                                // Check if there's non-walkable furniture on this tile
                                var items = gameMap.GetCoordinatedItems(new System.Drawing.Point(x, y));
                                bool hasFurnitureBlocking = false;
                                
                                if (items != null && items.Count > 0)
                                {
                                    foreach (var item in items)
                                    {
                                        if (item != null && item.GetBaseItem() != null && !item.GetBaseItem().Walkable)
                                        {
                                            hasFurnitureBlocking = true;
                                            break;
                                        }
                                    }
                                }
                                
                                if (hasFurnitureBlocking)
                                {
                                    // Mark tile as blocked for furniture
                                    heightMap += "x";
                                }
                                else
                                {
                                    // Use model height (furniture height is handled separately)
                                    double height = model.SqFloorHeight[x, y];
                                    heightMap += height.ToString("0");
                                }
                            }
                        }
                        if (y < model.MapSizeY - 1)
                            heightMap += "\r";
                    }
                    
                    // Debug log
                    if (heightMap != null)
                    {
                        Console.WriteLine($"[DEBUG] Room {room.Id} heightmap length: {heightMap.Length}");
                        Console.WriteLine($"[DEBUG] Room {room.Id} heightmap byte length: {Encoding.UTF8.GetByteCount(heightMap)}");
                        
                        // Check if heightmap is too large (max 65535 bytes for 2-byte length)
                        int byteLength = Encoding.UTF8.GetByteCount(heightMap);
                        if (byteLength > 65535)
                        {
                            Console.WriteLine($"[ERROR] Room {room.Id} heightmap is too large! {byteLength} bytes");
                            heightMap = "xxxxxxxxxxxxxxxxxxxx"; // Send blocked map as fallback
                        }
                        else if (heightMap.Length > 0)
                        {
                            Console.WriteLine($"[DEBUG] Room {room.Id} heightmap preview: {heightMap.Substring(0, Math.Min(100, heightMap.Length))}");
                        }
                    }
                    
                    packet.WriteString(heightMap ?? "");
                }
                else
                {
                    // Default
                    packet.WriteBoolean(true);
                    packet.WriteInteger(-1);
                    packet.WriteString("0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[ERROR] NitroRoomModelComposer error: {ex.Message}");
                Console.WriteLine($"[ERROR] Stack trace: {ex.StackTrace}");
                
                // Send default on error
                packet = new NitroServerPacket(1301);
                packet.WriteBoolean(true);
                packet.WriteInteger(-1);
                packet.WriteString("0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000");
            }
            
            return packet;
        }
    }
}
