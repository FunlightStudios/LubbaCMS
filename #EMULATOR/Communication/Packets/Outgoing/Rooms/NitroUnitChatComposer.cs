using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends unit chat message
    /// </summary>
    public class NitroUnitChatComposer
    {
        public static NitroServerPacket Compose(int virtualId, string message, int gesture = 0, int bubble = 0)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.UnitChat);
            
            // Room index (virtual ID)
            packet.WriteInteger(virtualId);
            
            // Message
            packet.WriteString(message);
            
            // Gesture (0 = none, 1 = wave, 2 = blow kiss, etc.)
            packet.WriteInteger(gesture);
            
            // Bubble style (0 = normal)
            packet.WriteInteger(bubble);
            
            // URLs count
            packet.WriteInteger(0);
            
            // Message length
            packet.WriteInteger(message.Length);
            
            return packet;
        }
    }
}
