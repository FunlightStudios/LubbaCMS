using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;
using System.Linq;
using log4net;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user's selected/wearing badges (for user profile/card)
    /// Header: 2998 (USER_BADGES)
    /// </summary>
    public class NitroUserBadgesComposer
    {
        private static readonly ILog log = LogManager.GetLogger(typeof(NitroUserBadgesComposer));
        
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(717); // USER_BADGES header (same as inventory badges)
            
            if (user == null || user.GetBadgeComponent() == null)
            {
                packet.WriteInteger(0); // User ID
                packet.WriteInteger(0); // Badge count
                return packet;
            }
            
            var badgeComponent = user.GetBadgeComponent();
            var activeBadges = badgeComponent.GetBadges()
                .Where(b => b.Slot > 0 && b.Slot <= 5)
                .OrderBy(b => b.Slot)
                .ToList();
            
            // Format: totalCount:int, activeCount:int, then all badges (id, code), then active badges (slot, code)
            // This matches the inventory badge format (717) which works
            var allBadges = badgeComponent.GetBadges().ToList();
            
            packet.WriteInteger(allBadges.Count); // Total badges
            
            int badgeId = 1;
            foreach (var badge in allBadges)
            {
                packet.WriteInteger(badgeId++);
                packet.WriteString(badge.Code);
            }
            
            packet.WriteInteger(activeBadges.Count); // Active badges count
            
            foreach (var badge in activeBadges)
            {
                packet.WriteInteger(badge.Slot); // Slot (1-5)
                packet.WriteString(badge.Code); // Badge code
            }
            
            log.Debug($"NitroUserBadgesComposer: Sent {activeBadges.Count} wearing badges for user {user.Username}");
            
            return packet;
        }
    }
}
