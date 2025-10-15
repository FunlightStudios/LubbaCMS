using System.Collections.Generic;
using System.Linq;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using Plus.HabboHotel.Items;
using log4net;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends all floor items (furniture) in a room
    /// Header: 1778 (FURNITURE_FLOOR)
    /// </summary>
    public class NitroRoomFloorItemsComposer
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.Packets.Outgoing.Rooms");
        
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1778); // FURNITURE_FLOOR header
            
            if (room == null || room.GetRoomItemHandler() == null)
            {
                // No room or items
                packet.WriteInteger(0); // Owner count
                packet.WriteInteger(0); // Item count
                return packet;
            }
            
            var itemHandler = room.GetRoomItemHandler();
            var floorItems = itemHandler.GetFloor.ToList();
            
            // Collect unique owners
            var owners = new Dictionary<int, string>();
            foreach (var item in floorItems)
            {
                if (item == null || item.UserID == 0) continue;
                
                if (!owners.ContainsKey(item.UserID))
                {
                    var habbo = PlusEnvironment.GetHabboById(item.UserID);
                    owners[item.UserID] = habbo?.Username ?? "Unknown";
                }
            }
            
            // Write owners
            packet.WriteInteger(owners.Count);
            foreach (var owner in owners)
            {
                packet.WriteInteger(owner.Key);    // User ID
                packet.WriteString(owner.Value);   // Username
            }
            
            // Write items
            packet.WriteInteger(floorItems.Count);
            
            foreach (var item in floorItems)
            {
                if (item == null) continue;
                
                WriteFloorItem(packet, item);
            }
            
            log.Debug($"Composed floor items packet: {owners.Count} owners, {floorItems.Count} items");
            
            return packet;
        }
        
        private static void WriteFloorItem(NitroServerPacket packet, Item item)
        {
            // Item ID
            packet.WriteInteger(item.Id);
            
            // Sprite ID (base item ID)
            packet.WriteInteger(item.GetBaseItem().SpriteId);
            
            // Position
            packet.WriteInteger(item.GetX);
            packet.WriteInteger(item.GetY);
            
            // Direction (rotation)
            packet.WriteInteger(item.Rotation);
            
            // Z position (height)
            packet.WriteString(item.GetZ.ToString("0.00"));
            
            // Stack height
            packet.WriteString(item.GetBaseItem().Height.ToString("0.00"));
            
            // Extra data (legacy)
            packet.WriteInteger(0);
            
            // Item data (state, etc.)
            WriteItemData(packet, item);
            
            // Expires (-1 = never)
            packet.WriteInteger(-1);
            
            // Usage policy (2 = owner only)
            packet.WriteInteger(2);
            
            // Owner user ID
            packet.WriteInteger(item.UserID);
            
            // Sprite name (if sprite ID < 0)
            if (item.GetBaseItem().SpriteId < 0)
            {
                packet.WriteString(item.GetBaseItem().ItemName);
            }
        }
        
        private static void WriteItemData(NitroServerPacket packet, Item item)
        {
            // Data type (0 = legacy string, 1 = map, 2 = string array, etc.)
            int dataType = 0; // Legacy string for now
            
            packet.WriteInteger(dataType);
            
            // Data value (item state/extra data)
            string dataValue = item.ExtraData ?? "0";
            packet.WriteString(dataValue);
            
            // Flags (for unique items, limited editions, etc.)
            // For now, we don't write any flags (no unique serial numbers)
        }
    }
}
