using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user's currently worn badges (displayed on avatar)
    /// Header: 1087 (USER_BADGES_CURRENT)
    /// </summary>
    public class NitroUserCurrentBadgesComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(1087); // USER_BADGES_CURRENT header
            
            if (user == null || user.GetBadgeComponent() == null)
            {
                // No user or badges
                packet.WriteInteger(0); // User ID (0 = unknown)
                packet.WriteInteger(0); // Badge count
                return packet;
            }
            
            // Write user ID
            packet.WriteInteger(user.Id);
            
            // Get active badges (slots 1-5)
            var allBadges = user.GetBadgeComponent().GetBadges();
            var activeBadges = new System.Collections.Generic.List<Plus.HabboHotel.Users.Badges.Badge>();
            
            foreach (var badge in allBadges)
            {
                if (badge.Slot > 0 && badge.Slot <= 5)
                {
                    activeBadges.Add(badge);
                }
            }
            
            // Write badge count
            packet.WriteInteger(activeBadges.Count);
            
            // Write each badge (slot, code)
            foreach (var badge in activeBadges)
            {
                packet.WriteInteger(badge.Slot); // Slot (1-5)
                packet.WriteString(badge.Code);  // Badge code
            }
            
            return packet;
        }
    }
}
