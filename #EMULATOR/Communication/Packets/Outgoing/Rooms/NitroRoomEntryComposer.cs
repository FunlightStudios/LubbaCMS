using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room entry confirmation to client
    /// </summary>
    public class NitroRoomEntryComposer
    {
        public static NitroServerPacket Compose(int roomId)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomEnter);
            
            return packet;
        }
    }
}
