using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room paint/visualization settings
    /// </summary>
    public class NitroRoomPaintComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomPaint);
            
            // Floor type
            packet.WriteString("111");
            
            // Wall type
            packet.WriteString("101");
            
            // Landscape type
            packet.WriteString("101");
            
            return packet;
        }
    }
}
