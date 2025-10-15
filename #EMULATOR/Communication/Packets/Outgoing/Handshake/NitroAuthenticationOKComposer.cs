namespace Plus.Communication.Packets.Outgoing.Handshake
{
    /// <summary>
    /// Nitro AuthenticationOK Packet
    /// Sent after successful SSO authentication
    /// </summary>
    public class NitroAuthenticationOKComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.AuthenticationOK);
            
            // Authentication successful - no data needed
            
            return packet;
        }
    }
}
