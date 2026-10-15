using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room rights level to client
    /// Header: 780 (ROOM_RIGHTS)
    /// </summary>
    public class NitroRoomRightsComposer
    {
        public static NitroServerPacket Compose(int controllerLevel)
        {
            // Header 780 = ROOM_RIGHTS
            // Controller levels:
            // 0 = NONE (no rights)
            // 1 = GUEST (can place furniture if room allows)
            // 2 = RIGHTS (has rights, can place furniture)
            // 3 = MODERATOR (room moderator)
            // 4 = OWNER (room owner)
            var packet = new NitroServerPacket(780);
            
            // Controller level
            packet.WriteInteger(controllerLevel);
            
            return packet;
        }
    }
}
