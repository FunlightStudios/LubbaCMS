using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user perks/features to client
    /// </summary>
    public class NitroUserPerksComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.UserPerks);
            
            // Trade enabled
            packet.WriteBoolean(true);
            
            // Can buy from catalog
            packet.WriteBoolean(true);
            
            // Can use camera
            packet.WriteBoolean(true);
            
            // Builder club
            packet.WriteBoolean(user.VIPRank > 0);
            
            // Citizenship
            packet.WriteBoolean(true);
            
            return packet;
        }
    }
}
