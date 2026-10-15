using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;
using System.Collections.Generic;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends user chatlog to Nitro mod tools
    /// Header: 3377 (MODTOOL_USER_CHATLOG)
    /// </summary>
    public class NitroModToolUserChatlogComposer
    {
        public static NitroServerPacket Compose(Habbo user, List<ModToolUserChatlogEntry> chatlogs)
        {
            var packet = new NitroServerPacket(3377);
            
            // User info
            packet.WriteInteger(user != null ? user.Id : 0);
            packet.WriteString(user != null ? user.Username : "Unknown");
            
            // Chatlog entries
            packet.WriteInteger(chatlogs != null ? chatlogs.Count : 0);
            
            if (chatlogs != null)
            {
                foreach (var entry in chatlogs)
                {
                    packet.WriteInteger(entry.Timestamp); // Timestamp
                    packet.WriteInteger(entry.RoomId); // Room ID
                    packet.WriteString(entry.RoomName); // Room name
                    packet.WriteString(entry.Message); // Message
                }
            }
            
            return packet;
        }
    }
    
    public class ModToolUserChatlogEntry
    {
        public int Timestamp { get; set; }
        public int RoomId { get; set; }
        public string RoomName { get; set; }
        public string Message { get; set; }
    }
}
