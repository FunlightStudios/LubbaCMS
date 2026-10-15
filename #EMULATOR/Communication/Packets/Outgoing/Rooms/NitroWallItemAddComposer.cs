using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends a single wall item add notification to Nitro client
    /// Header: 2187 (ITEM_WALL_ADD)
    /// </summary>
    public class NitroWallItemAddComposer
    {
        public static NitroServerPacket Compose(Item item, string username)
        {
            var packet = new NitroServerPacket(2187);

            WriteWallItemData(packet, item);
            packet.WriteString(username);

            return packet;
        }

        private static void WriteWallItemData(NitroServerPacket packet, Item item)
        {
            packet.WriteString(item.Id.ToString()); // Item ID as string
            packet.WriteInteger(item.GetBaseItem().SpriteId); // Sprite ID
            packet.WriteString(item.wallCoord ?? ""); // Wall location string

            // Stuff data (state)
            packet.WriteString(item.ExtraData ?? "0");

            packet.WriteInteger(-1); // Expires (-1 = never)
            packet.WriteInteger(item.GetBaseItem().InteractionType == HabboHotel.Items.InteractionType.NONE ? 0 : 1); // Usage policy
            packet.WriteInteger(item.UserID); // Owner user ID
        }
    }
}
