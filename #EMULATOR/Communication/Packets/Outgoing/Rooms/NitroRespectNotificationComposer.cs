using Plus.Communication.WebSocket;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends respect notification to user
    /// Header: 2815 (RESPECT_RECEIVED)
    /// </summary>
    public class NitroRespectNotificationComposer
    {
        public static NitroServerPacket Compose(int userId, int totalRespect)
        {
            var packet = new NitroServerPacket(2815); // RESPECT_RECEIVED
            packet.WriteInteger(userId);
            packet.WriteInteger(totalRespect);
            return packet;
        }
    }
}
