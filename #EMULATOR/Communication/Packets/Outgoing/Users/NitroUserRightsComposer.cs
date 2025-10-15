using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user rights/permissions to client
    /// </summary>
    public class NitroUserRightsComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.UserRights);
            
            // Club level (0 = normal, 1 = VIP, 2 = HC)
            packet.WriteInteger(user.VIPRank);
            
            // Security level (0 = user, 1 = mod, 2 = admin)
            int securityLevel = 0;
            if (user.GetPermissions().HasRight("mod_tool"))
                securityLevel = 1;
            if (user.GetPermissions().HasRight("acc_supporttool"))
                securityLevel = 2;
                
            packet.WriteInteger(securityLevel);
            
            // Ambassador level
            packet.WriteInteger(0);
            
            return packet;
        }
    }
}
