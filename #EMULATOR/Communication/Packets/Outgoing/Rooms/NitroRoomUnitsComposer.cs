using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using Plus.HabboHotel.Users;
using System.Collections.Generic;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends ALL units (user + other users + bots) in ONE packet for room entry
    /// This prevents Nitro from overwriting the user with bot data
    /// </summary>
    public class NitroRoomUnitsComposer
    {
        public static NitroServerPacket Compose(Habbo user, int userVirtualId, int userX, int userY, double userZ, List<RoomUser> bots, List<RoomUser> otherUsers = null, int userRotation = 2)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.Unit);
            
            // Total unit count = 1 current user + X other users + Y bots
            int otherUserCount = otherUsers?.Count ?? 0;
            packet.WriteInteger(1 + otherUserCount + bots.Count);
            
            // ========== WRITE CURRENT USER FIRST (Type=1) ==========
            packet.WriteInteger(user.Id); // User ID
            packet.WriteString(user.Username); // Username
            packet.WriteString(user.Motto); // Motto
            packet.WriteString(user.Look); // Figure
            packet.WriteInteger(userVirtualId); // Room index (VirtualId)
            packet.WriteInteger(userX); // X
            packet.WriteInteger(userY); // Y
            packet.WriteString(userZ.ToString("0.00")); // Z height
            packet.WriteInteger(userRotation); // Direction from RoomUser
            packet.WriteInteger(1); // Type: 1 = user
            
            // Type 1 (User) specific data:
            packet.WriteString(user.Gender.ToUpper()); // Gender
            packet.WriteInteger(0); // Group ID
            packet.WriteInteger(0); // Group status
            packet.WriteString(""); // Group name
            packet.WriteString(""); // Swim figure
            packet.WriteInteger(user.GetStats()?.AchievementPoints ?? 0); // Activity points
            packet.WriteBoolean(user.GetPermissions()?.HasRight("mod_tool") ?? false); // Is moderator
            
            // ========== WRITE OTHER USERS (Type=1) ==========
            if (otherUsers != null)
            {
                foreach (var roomUser in otherUsers)
                {
                    // ALWAYS use UserId to get Habbo (works for both Flash and Nitro clients)
                    var otherHabbo = PlusEnvironment.GetHabboById(roomUser.UserId);
                    
                    if (otherHabbo != null)
                    {
                        packet.WriteInteger(otherHabbo.Id); // User ID
                        packet.WriteString(otherHabbo.Username); // Username
                        packet.WriteString(otherHabbo.Motto); // Motto
                        packet.WriteString(otherHabbo.Look); // Figure
                        packet.WriteInteger(roomUser.VirtualId); // Room index (VirtualId)
                        packet.WriteInteger(roomUser.X); // X
                        packet.WriteInteger(roomUser.Y); // Y
                        packet.WriteString(roomUser.Z.ToString("0.00")); // Z height
                        packet.WriteInteger(roomUser.RotBody); // Direction (0-7)
                        packet.WriteInteger(1); // Type: 1 = user
                        
                        // Type 1 (User) specific data:
                        packet.WriteString(otherHabbo.Gender.ToUpper()); // Gender
                        packet.WriteInteger(0); // Group ID
                        packet.WriteInteger(0); // Group status
                        packet.WriteString(""); // Group name
                        packet.WriteString(""); // Swim figure
                        packet.WriteInteger(otherHabbo.GetStats()?.AchievementPoints ?? 0); // Activity points
                        packet.WriteBoolean(otherHabbo.GetPermissions()?.HasRight("mod_tool") ?? false); // Is moderator
                    }
                }
            }
            
            // ========== WRITE BOTS (Type=3) ==========
            foreach (var bot in bots)
            {
                packet.WriteInteger(bot.BotData.Id * -1); // Bot ID (negative to avoid collision!)
                packet.WriteString(bot.BotData.Name); // Bot name
                packet.WriteString(bot.BotData.Motto); // Bot motto
                packet.WriteString(bot.BotData.Look); // Bot figure
                packet.WriteInteger(bot.VirtualId); // Room index
                packet.WriteInteger(bot.X); // X
                packet.WriteInteger(bot.Y); // Y
                packet.WriteString(bot.Z.ToString("0.00")); // Z height
                packet.WriteInteger(bot.RotBody); // Direction
                packet.WriteInteger(3); // Type: 3 = bot
                
                // Type 3 (Bot) has NO additional data!
            }
            
            return packet;
        }
    }
}
