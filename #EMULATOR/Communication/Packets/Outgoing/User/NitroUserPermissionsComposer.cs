using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user permissions including club level
    /// Header: 2811 (USER_PERMISSIONS)
    /// </summary>
    public class NitroUserPermissionsComposer
    {
        public static NitroServerPacket Compose(int clubLevel, int securityLevel, bool isAmbassador)
        {
            var packet = new NitroServerPacket(2811); // USER_PERMISSIONS header
            
            // Club level (0 = no club, 1 = basic club, 2 = VIP club)
            packet.WriteInteger(clubLevel);
            
            // Security level (staff rank)
            packet.WriteInteger(securityLevel);
            
            // Is ambassador
            packet.WriteBoolean(isAmbassador);
            
            return packet;
        }
    }
}
