using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms.Permissions
{
    /// <summary>
    /// Notifies that a user was given room rights
    /// Header: 2088 (ROOM_RIGHTS_LIST_ADD / FlatControllerAddedEvent) - used by Nitro
    /// Format: [roomId:int][userId:int][username:string]
    /// </summary>
    public class NitroFlatControllerAddedComposer
    {
        public static NitroServerPacket Compose(int roomId, int userId, string username)
        {
            var packet = new NitroServerPacket(2088);

            packet.WriteInteger(roomId);
            packet.WriteInteger(userId);
            packet.WriteString(username ?? string.Empty);

            return packet;
        }
    }
}
