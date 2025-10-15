using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Navigator
{
    /// <summary>
    /// Sends navigator settings to client
    /// </summary>
    public class NitroNavigatorSettingsComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorSettings);
            
            // Arcturus/Nitro format
            packet.WriteInteger(0); // Home room ID
            packet.WriteInteger(0); // Room ID to enter
            
            return packet;
        }
    }
}
