using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room heightmap to client
    /// </summary>
    public class NitroHeightMapComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomHeightMap);
            
            if (room != null && room.GetGameMap() != null)
            {
                var gameMap = room.GetGameMap();
                var model = gameMap.Model;
                
                // Map size
                packet.WriteInteger(model.MapSizeX);
                packet.WriteInteger(model.MapSizeY);
                
                // Fixed tile height (for now, use 0)
                packet.WriteInteger(0);
                
                // Heightmap string with dynamic tile states (including furniture blocking)
                string heightMap = "";
                for (int y = 0; y < model.MapSizeY; y++)
                {
                    for (int x = 0; x < model.MapSizeX; x++)
                    {
                        // Check if tile is blocked by model
                        if (model.SqState[x, y] == HabboHotel.Rooms.SquareState.BLOCKED)
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
                                // Use dynamic height (includes furniture height for walkable items)
                                double height = gameMap.GetHeightForSquare(new System.Drawing.Point(x, y));
                                if (height == 0)
                                {
                                    // Use model height if no furniture
                                    height = model.SqFloorHeight[x, y];
                                }
                                heightMap += height.ToString("0");
                            }
                        }
                    }
                    if (y < model.MapSizeY - 1)
                        heightMap += "\r";
                }
                
                packet.WriteString(heightMap);
            }
            else
            {
                // Default empty room
                packet.WriteInteger(10);
                packet.WriteInteger(10);
                packet.WriteInteger(0);
                packet.WriteString("0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000\r0000000000");
            }
            
            return packet;
        }
    }
}
