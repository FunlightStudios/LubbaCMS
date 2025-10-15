using System.Collections.Generic;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;
using Plus.HabboHotel.Users.Badges;
using log4net;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends user badges inventory
    /// Header: 717 (USER_BADGES)
    /// </summary>
    public class NitroBadgesComposer
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.Packets.Outgoing.Inventory");
        
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(717); // USER_BADGES header
            
            if (user == null || user.GetBadgeComponent() == null)
            {
                log.Warn("NitroBadgesComposer: User or BadgeComponent is null");
                // No badges
                packet.WriteInteger(0); // Total badges count
                packet.WriteInteger(0); // Active badges count
                return packet;
            }
            
            var badgeComponent = user.GetBadgeComponent();
            var allBadges = badgeComponent.GetBadges();
            
            log.Info($"NitroBadgesComposer: User {user.Username} has {allBadges.Count} badges");
            
            // Write total badges count
            packet.WriteInteger(allBadges.Count);
            
            // Write all badges (badgeId, badgeCode)
            int badgeId = 1;
            foreach (var badge in allBadges)
            {
                log.Debug($"  Badge: {badge.Code} (Slot: {badge.Slot})");
                packet.WriteInteger(badgeId); // Badge ID (incremental)
                packet.WriteString(badge.Code); // Badge code
                badgeId++;
            }
            
            // Write active badges count (max 5 slots)
            var activeBadges = new List<Badge>();
            foreach (var badge in allBadges)
            {
                if (badge.Slot > 0 && badge.Slot <= 5)
                {
                    activeBadges.Add(badge);
                }
            }
            
            log.Info($"NitroBadgesComposer: {activeBadges.Count} active badges");
            packet.WriteInteger(activeBadges.Count);
            
            // Write active badges (slot, badgeCode)
            foreach (var badge in activeBadges)
            {
                log.Debug($"  Active Badge: Slot {badge.Slot} = {badge.Code}");
                packet.WriteInteger(badge.Slot); // Badge slot (1-5)
                packet.WriteString(badge.Code); // Badge code
            }
            
            log.Info($"NitroBadgesComposer: Sent {allBadges.Count} total badges, {activeBadges.Count} active");
            return packet;
        }
    }
}
