using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room wall thickness
    /// </summary>
    public class NitroRoomThicknessComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomThickness);
            
            // Hide walls
            packet.WriteBoolean(false);
            
            // Wall thickness (-2 to 1, where 0 = normal)
            packet.WriteInteger(0);
            
            // Floor thickness (-2 to 1, where 0 = normal)
            packet.WriteInteger(0);
            
            return packet;
        }
    }
}
