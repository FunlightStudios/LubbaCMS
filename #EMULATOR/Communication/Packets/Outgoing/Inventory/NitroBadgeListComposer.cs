using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends badge inventory to client
    /// </summary>
    public class NitroBadgeListComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.BadgeList);
            
            var badgeComponent = user.GetBadgeComponent();
            
            if (badgeComponent != null)
            {
                var badges = badgeComponent.GetBadges().ToList();
                
                // Total badges
                packet.WriteInteger(badges.Count);
                
                // Badges array
                foreach (var badge in badges)
                {
                    packet.WriteInteger(badge.Slot);
                    packet.WriteString(badge.Code);
                }
            }
            else
            {
                // No badges
                packet.WriteInteger(0);
            }
            
            return packet;
        }
    }
}
