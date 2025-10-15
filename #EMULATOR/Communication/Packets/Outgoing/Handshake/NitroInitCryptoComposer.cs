namespace Plus.Communication.Packets.Outgoing.Handshake
{
    /// <summary>
    /// Nitro InitCrypto Packet
    /// Sent when client connects to initialize encryption (Nitro doesn't use encryption by default)
    /// </summary>
    public class NitroInitCryptoComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.InitCrypto);
            
            // Nitro doesn't require encryption, send empty crypto
            packet.WriteString(""); // Token
            packet.WriteBoolean(false); // Encrypted
            
            return packet;
        }
    }
}
