using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms.Permissions
{
    /// <summary>
    /// Notifies that a user's room rights were removed
    /// Header: 1327 (ROOM_RIGHTS_LIST_REMOVE / FlatControllerRemovedEvent) - used by Nitro
    /// Format: [roomId:int][userId:int]
    /// </summary>
    public class NitroFlatControllerRemovedComposer
    {
        public static NitroServerPacket Compose(int roomId, int userId)
        {
            var packet = new NitroServerPacket(1327);

            packet.WriteInteger(roomId);
            packet.WriteInteger(userId);

            return packet;
        }
    }
}
