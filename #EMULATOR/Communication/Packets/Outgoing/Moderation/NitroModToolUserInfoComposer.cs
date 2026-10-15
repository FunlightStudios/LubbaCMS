using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;
using System;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends user info to Nitro mod tools
    /// Header: 1333 (MODTOOL_ROOM_INFO) - Actually used for user info in Nitro
    /// </summary>
    public class NitroModToolUserInfoComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(1333);
            
            if (user == null)
            {
                packet.WriteInteger(0); // User ID
                packet.WriteString("Unknown"); // Username
                packet.WriteString(""); // Figure
                packet.WriteInteger(0); // Registration timestamp
                packet.WriteInteger(0); // Last login timestamp
                packet.WriteBoolean(false); // Online
                packet.WriteInteger(0); // CFH count
                packet.WriteInteger(0); // Abusive CFH count
                packet.WriteInteger(0); // Caution count
                return packet;
            }
            
            // User basic info
            packet.WriteInteger(user.Id); // User ID
            packet.WriteString(user.Username); // Username
            packet.WriteString(user.Look); // Figure
            
            // Timestamps (Unix timestamps)
            packet.WriteInteger((int)user.AccountCreated); // Registration timestamp
            packet.WriteInteger((int)(DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0, DateTimeKind.Utc)).TotalSeconds); // Last login (now)
            
            // Status
            packet.WriteBoolean(true); // Online (always true if we have the user object)
            
            // Moderation stats
            packet.WriteInteger(0); // CFH (Call for Help) count
            packet.WriteInteger(0); // Abusive CFH count
            packet.WriteInteger(0); // Caution count
            
            return packet;
        }
    }
}
