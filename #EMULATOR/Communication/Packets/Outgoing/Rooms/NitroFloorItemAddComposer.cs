using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends floor item add notification to Nitro client
    /// Header: 1534 (FURNITURE_FLOOR_ADD)
    /// </summary>
    public class NitroFloorItemAddComposer
    {
        public static NitroServerPacket Compose(Item item, string username)
        {
            var packet = new NitroServerPacket(1534);
            
            // Write item data
            WriteFloorItemData(packet, item);
            
            // Write owner username
            packet.WriteString(username);
            
            return packet;
        }
        
        private static void WriteFloorItemData(NitroServerPacket packet, Item item)
        {
            packet.WriteInteger(item.Id); // Item ID
            packet.WriteInteger(item.GetBaseItem().SpriteId); // Sprite ID
            packet.WriteInteger(item.GetX); // X position
            packet.WriteInteger(item.GetY); // Y position
            packet.WriteInteger(item.Rotation); // Direction (0-7)
            packet.WriteString(item.GetZ.ToString("0.00")); // Z height (as string!)
            packet.WriteString(item.GetBaseItem().Height.ToString("0.00")); // Stack height (as string!)
            packet.WriteInteger(0); // Extra (legacy)
            
            // Write furniture data (MapStuffData format)
            WriteFurnitureData(packet, item);
            
            packet.WriteInteger(-1); // Expires (-1 = never)
            packet.WriteInteger(item.GetBaseItem().InteractionType == HabboHotel.Items.InteractionType.NONE ? 0 : 1); // Usage policy
            packet.WriteInteger(item.UserID); // Owner user ID
        }
        
        private static void WriteFurnitureData(NitroServerPacket packet, Item item)
        {
            // Data flags and type
            packet.WriteInteger(0); // Flags
            packet.WriteInteger(1); // Type: 1 = MapStuffData (legacy string)
            
            // Map data count
            packet.WriteInteger(1);
            
            // State key-value
            packet.WriteString("state");
            packet.WriteString(item.ExtraData);
        }
    }
}
