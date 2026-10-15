using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Notifications
{
    /// <summary>
    /// Sends a small bubble notification (appears under currencies, top-right)
    /// Header: 1992 (NOTIFICATION_DIALOG / Bubble Alert)
    /// </summary>
    public class NitroBubbleNotificationComposer
    {
        public static NitroServerPacket Compose(string message)
        {
            var packet = new NitroServerPacket(1992); // NOTIFICATION_DIALOG header for Nitro
            packet.WriteString("furni_placement_error"); // Notification type/image
            packet.WriteInteger(1); // Parameter count
            packet.WriteString("message");
            packet.WriteString(message);
            return packet;
        }
    }
}
