using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends user subscription (Habbo Club) information
    /// Header: 954 (USER_SUBSCRIPTION)
    /// </summary>
    public class NitroUserSubscriptionComposer
    {
        public static NitroServerPacket Compose(string subscriptionName, int days, int int1, int months, int years, 
            bool hasEverBeenMember, bool isVip, int pastClubDays, int pastVIPDays, int totalSeconds)
        {
            var packet = new NitroServerPacket(954); // USER_SUBSCRIPTION header
            
            // Subscription name (e.g., "habbo_club")
            packet.WriteString(subscriptionName);
            
            // Days remaining in current period
            packet.WriteInteger(days);
            
            // Unknown int (usually 0 or 1)
            packet.WriteInteger(int1);
            
            // Months of subscription
            packet.WriteInteger(months);
            
            // Years of subscription (3 = lifetime)
            packet.WriteInteger(years);
            
            // Has ever been a member
            packet.WriteBoolean(hasEverBeenMember);
            
            // Is VIP (Habbo Club VIP)
            packet.WriteBoolean(isVip);
            
            // Past club days
            packet.WriteInteger(pastClubDays);
            
            // Past VIP days
            packet.WriteInteger(pastVIPDays);
            
            // Total seconds until expiration
            packet.WriteInteger(totalSeconds);
            
            return packet;
        }
        
        /// <summary>
        /// Create a Habbo Club subscription with specified days
        /// </summary>
        public static NitroServerPacket ComposeHabboClub(int days, bool isVip = false)
        {
            int months = days / 31;
            int remainingDays = days % 31;
            int totalSeconds = days * 24 * 60 * 60; // Convert days to seconds
            
            return Compose(
                "habbo_club",      // subscription name
                remainingDays,     // days in current period
                1,                 // int1
                months,            // months
                0,                 // years (0 = not lifetime)
                true,              // has ever been member
                isVip,             // is VIP
                days,              // past club days
                isVip ? days : 0,  // past VIP days
                totalSeconds       // total seconds
            );
        }
        
        /// <summary>
        /// Create a lifetime Habbo Club subscription
        /// </summary>
        public static NitroServerPacket ComposeLifetimeClub(bool isVip = false)
        {
            return Compose(
                "habbo_club",      // subscription name
                31,                // days (always 31 for lifetime)
                1,                 // int1
                999,               // months (high number)
                3,                 // years (3 = lifetime indicator)
                true,              // has ever been member
                isVip,             // is VIP
                99999,             // past club days
                isVip ? 99999 : 0, // past VIP days
                int.MaxValue       // total seconds (max value for lifetime)
            );
        }
    }
}
