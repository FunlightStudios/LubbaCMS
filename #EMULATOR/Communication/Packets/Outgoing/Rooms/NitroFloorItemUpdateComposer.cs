using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends floor item update notification to Nitro client
    /// Header: 3776 (FURNITURE_FLOOR_UPDATE)
    /// </summary>
    public class NitroFloorItemUpdateComposer
    {
        public static NitroServerPacket Compose(Item item)
        {
            var packet = new NitroServerPacket(3776);
            
            // Write item data (same structure as Add)
            packet.WriteInteger(item.Id); // Item ID
            packet.WriteInteger(item.GetBaseItem().SpriteId); // Sprite ID
            packet.WriteInteger(item.GetX); // X position
            packet.WriteInteger(item.GetY); // Y position
            packet.WriteInteger(item.Rotation); // Direction (0-7)
            packet.WriteString(item.GetZ.ToString("0.00")); // Z height (as string!)
            packet.WriteString(item.GetBaseItem().Height.ToString("0.00")); // Stack height (as string!)
            packet.WriteInteger(0); // Extra (legacy)
            
            // Write furniture data (Legacy String format to match initial list)
            WriteFurnitureData(packet, item);
            
            packet.WriteInteger(-1); // Expires (-1 = never)
            packet.WriteInteger(2); // Usage policy (match initial composer: 2 = owner only)
            packet.WriteInteger(item.UserID); // Owner user ID
            
            return packet;
        }
        
        private static void WriteFurnitureData(NitroServerPacket packet, Item item)
        {
            // Data type (0 = legacy string) - match NitroRoomFloorItemsComposer
            packet.WriteInteger(0);
            
            // Data value (item state/extra data)
            packet.WriteString(item.ExtraData ?? "0");
        }
    }
}
