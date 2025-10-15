using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends unit dance animation
    /// Header: 2233 (UNIT_DANCE)
    /// </summary>
    public class NitroUnitDanceComposer
    {
        public static NitroServerPacket Compose(int unitId, int danceId)
        {
            var packet = new NitroServerPacket(2233); // UNIT_DANCE header
            
            // Unit ID (virtual ID in room)
            packet.WriteInteger(unitId);
            
            // Dance ID (0 = stop, 1-4 = different dance styles)
            packet.WriteInteger(danceId);
            
            return packet;
        }
    }
}
