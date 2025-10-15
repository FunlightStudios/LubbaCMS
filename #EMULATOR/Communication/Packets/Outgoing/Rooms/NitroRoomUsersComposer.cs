using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using Plus.HabboHotel.Users;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends users in room to client
    /// </summary>
    public class NitroRoomUsersComposer
    {
        public static NitroServerPacket ComposeUser(Habbo habbo, int virtualId, int x, int y, double z = 0.0)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.Unit);
            
            // User count
            packet.WriteInteger(1);
            
            // User data (CORRECT format from RoomUnitParser)
            packet.WriteInteger(habbo.Id); // ID
            packet.WriteString(habbo.Username); // Username
            packet.WriteString(habbo.Motto); // Custom (motto)
            packet.WriteString(habbo.Look); // Figure
            packet.WriteInteger(virtualId); // Room index
            packet.WriteInteger(x); // X
            packet.WriteInteger(y); // Y
            packet.WriteString(z.ToString("0.00")); // Z height
            packet.WriteInteger(2); // Direction (0-7)
            packet.WriteInteger(1); // Type: 1 = user, 2 = pet, 3 = bot
            
            // Type 1 (User) specific data:
            packet.WriteString(habbo.Gender.ToUpper()); // Sex
            packet.WriteInteger(0); // Group ID
            packet.WriteInteger(0); // Group status
            packet.WriteString(""); // Group name
            packet.WriteString(""); // Swim figure (empty = no swimming)
            packet.WriteInteger(habbo.GetStats()?.AchievementPoints ?? 0); // Activity points
            packet.WriteBoolean(habbo.GetPermissions()?.HasRight("mod_tool") ?? false); // Is moderator
            
            return packet;
        }
        
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.Unit);
            
            if (room != null)
            {
                var users = room.GetRoomUserManager().GetRoomUsers().ToList();
                
                // User count
                packet.WriteInteger(users.Count);
                
                // Users array
                foreach (var user in users)
                {
                    if (user.GetClient() == null || user.GetClient().GetHabbo() == null)
                        continue;
                        
                    var habbo = user.GetClient().GetHabbo();
                    
                    packet.WriteInteger(habbo.Id);
                    packet.WriteString(habbo.Username);
                    packet.WriteString(habbo.Motto);
                    packet.WriteString(habbo.Look);
                    packet.WriteInteger(user.VirtualId);
                    packet.WriteInteger(user.X);
                    packet.WriteInteger(user.Y);
                    packet.WriteString(user.Z.ToString("0.00"));
                    packet.WriteInteger(user.RotBody);
                    packet.WriteInteger(1); // 1 = user, 2 = pet, 3 = bot
                    packet.WriteString(habbo.Gender.ToUpper());
                    packet.WriteInteger(0);
                    packet.WriteString("");
                    packet.WriteString("");
                    packet.WriteString("");
                    packet.WriteInteger(habbo.GetStats()?.AchievementPoints ?? 0);
                    packet.WriteBoolean(habbo.GetPermissions().HasRight("mod_tool"));
                }
            }
            else
            {
                // No users
                packet.WriteInteger(0);
            }
            
            return packet;
        }
    }
}
