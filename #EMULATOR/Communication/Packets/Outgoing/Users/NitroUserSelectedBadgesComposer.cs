using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Nitro variant of HabboUserBadgesMessageComposer.
    /// Sends selected/worn badges for a specific user.
    /// Header: 3269
    /// </summary>
    public class NitroUserSelectedBadgesComposer
    {
        public static NitroServerPacket Compose(Habbo habbo)
        {
            var packet = new NitroServerPacket(3269);

            if (habbo == null || habbo.GetBadgeComponent() == null)
            {
                packet.WriteInteger(0); // user id
                packet.WriteInteger(0); // count
                return packet;
            }

            packet.WriteInteger(habbo.Id);

            var badges = habbo.GetBadgeComponent()
                .GetBadges()
                .Where(b => b.Slot > 0 && b.Slot <= 5)
                .OrderBy(b => b.Slot)
                .ToList();

            packet.WriteInteger(badges.Count);
            foreach (var badge in badges)
            {
                packet.WriteInteger(badge.Slot);
                packet.WriteString(badge.Code);
            }

            return packet;
        }
    }
}
