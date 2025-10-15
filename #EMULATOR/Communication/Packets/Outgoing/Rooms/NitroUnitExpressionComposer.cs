using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends unit expression (wave, laugh, etc.)
    /// Header: 1631 (UNIT_EXPRESSION)
    /// </summary>
    public class NitroUnitExpressionComposer
    {
        public static NitroServerPacket Compose(int unitId, int expressionId)
        {
            var packet = new NitroServerPacket(1631); // UNIT_EXPRESSION header
            
            // Unit ID (virtual ID in room)
            packet.WriteInteger(unitId);
            
            // Expression ID (1 = wave, 2 = blow, 3 = laugh, etc.)
            packet.WriteInteger(expressionId);
            
            return packet;
        }
    }
}
