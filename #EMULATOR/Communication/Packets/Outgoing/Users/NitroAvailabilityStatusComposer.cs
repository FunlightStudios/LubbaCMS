using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends availability status (hotel open/closed)
    /// </summary>
    public class NitroAvailabilityStatusComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.AvailabilityStatus);
            
            // Hotel is open
            packet.WriteBoolean(true);
            
            // No maintenance
            packet.WriteBoolean(false);
            
            return packet;
        }
    }
}
