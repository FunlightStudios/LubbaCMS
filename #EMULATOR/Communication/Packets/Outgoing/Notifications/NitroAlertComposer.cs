using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Notifications
{
    /// <summary>
    /// Sends a generic alert notification to Nitro client
    /// Header: 3801 (GENERIC_ALERT)
    /// </summary>
    public class NitroAlertComposer
    {
        public static NitroServerPacket Compose(string message)
        {
            var packet = new NitroServerPacket(3801);
            packet.WriteString(message); // Alert message
            return packet;
        }
    }
}
