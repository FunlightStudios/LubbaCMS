using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using System.Collections.Generic;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends room chatlog to Nitro mod tools
    /// Header: 3434 (MODTOOL_ROOM_CHATLOG)
    /// </summary>
    public class NitroModToolRoomChatlogComposer
    {
        public static NitroServerPacket Compose(Room room, List<ModToolRoomChatlogEntry> chatlogs)
        {
            var packet = new NitroServerPacket(3434);
            
            // Room info
            packet.WriteInteger(room != null ? room.Id : 0);
            packet.WriteString(room != null ? room.Name : "Unknown");
            
            // Chatlog entries
            packet.WriteInteger(chatlogs != null ? chatlogs.Count : 0);
            
            if (chatlogs != null)
            {
                foreach (var entry in chatlogs)
                {
                    packet.WriteInteger(entry.Timestamp); // Timestamp
                    packet.WriteInteger(entry.UserId); // User ID
                    packet.WriteString(entry.Username); // Username
                    packet.WriteString(entry.Message); // Message
                }
            }
            
            return packet;
        }
    }
    
    public class ModToolRoomChatlogEntry
    {
        public int Timestamp { get; set; }
        public int UserId { get; set; }
        public string Username { get; set; }
        public string Message { get; set; }
    }
}
