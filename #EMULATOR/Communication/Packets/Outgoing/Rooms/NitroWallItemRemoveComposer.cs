using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends wall item remove notification to Nitro client
    /// Header: 3208 (ITEM_WALL_REMOVE)
    /// </summary>
    public class NitroWallItemRemoveComposer
    {
        public static NitroServerPacket Compose(int itemId, int userId)
        {
            var packet = new NitroServerPacket(3208);

            // Item ID as string (matches FurnitureWallRemoveParser)
            packet.WriteString(itemId.ToString());
            packet.WriteInteger(userId);

            return packet;
        }
    }
}
