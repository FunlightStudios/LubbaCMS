using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room forward packet (redirects client to a room)
    /// </summary>
    public class NitroRoomForwardComposer
    {
        public static NitroServerPacket Compose(int roomId)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomForward);
            
            packet.WriteInteger(roomId);
            
            return packet;
        }
    }
}
