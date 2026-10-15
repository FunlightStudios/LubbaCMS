using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends wall item update notification to Nitro client
    /// Header: 2009 (ITEM_WALL_UPDATE)
    /// </summary>
    public class NitroWallItemUpdateComposer
    {
        public static NitroServerPacket Compose(Item item)
        {
            var packet = new NitroServerPacket(2009);

            // Item ID as string
            packet.WriteString(item.Id.ToString());

            // Sprite ID (base item ID)
            packet.WriteInteger(item.GetBaseItem().SpriteId);

            // Wall location
            packet.WriteString(item.wallCoord ?? "");

            // Stuff data (state)
            packet.WriteString(item.ExtraData ?? "0");

            // Expires (-1 = never)
            packet.WriteInteger(-1);

            // Usage policy
            packet.WriteInteger(item.GetBaseItem().InteractionType == HabboHotel.Items.InteractionType.NONE ? 0 : 1);

            // Owner user ID
            packet.WriteInteger(item.UserID);

            return packet;
        }
    }
}
