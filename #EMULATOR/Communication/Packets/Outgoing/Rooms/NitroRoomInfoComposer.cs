using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room info to client
    /// </summary>
    public class NitroRoomInfoComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(687); // ROOM_INFO header
            
            // Room entered flag
            packet.WriteBoolean(true);
            
            // Room data (RoomDataParser - COMPLETE format from Navigator)
            packet.WriteInteger(room.Id);
            packet.WriteString(room.Name);
            packet.WriteInteger(room.OwnerId);
            packet.WriteString(room.OwnerName);
            packet.WriteInteger((int)room.Access);
            packet.WriteInteger(room.UsersNow);
            packet.WriteInteger(room.UsersMax);
            packet.WriteString(room.Description ?? "");
            packet.WriteInteger(0); // Trade mode
            packet.WriteInteger(room.Score);
            packet.WriteInteger(0); // Ranking
            packet.WriteInteger(room.Category);
            
            // Tags
            packet.WriteInteger(0); // Tag count
            
            // BitMask for optional data
            int bitMask = 8; // Show owner
            if (room.AllowPets == 1) bitMask |= 16; // Allow pets
            packet.WriteInteger(bitMask);
            // No optional data (thumbnail, group, ad) since bitMask doesn't include them
            
            // Room forward flag
            packet.WriteBoolean(false);
            
            // Room picker flag
            packet.WriteBoolean(false);
            
            // Is group member
            packet.WriteBoolean(false);
            
            // All in room muted
            packet.WriteBoolean(false);
            
            // Moderation settings (RoomModerationParser)
            packet.WriteInteger(0); // Who can mute (0 = owner only)
            packet.WriteInteger(0); // Who can kick (0 = owner only)
            packet.WriteInteger(0); // Who can ban (0 = owner only)
            
            // Can mute
            packet.WriteBoolean(false);
            
            // Chat settings (RoomChatParser)
            packet.WriteInteger(0); // Chat mode
            packet.WriteInteger(0); // Chat weight
            packet.WriteInteger(0); // Chat speed
            packet.WriteInteger(0); // Chat distance
            packet.WriteInteger(0); // Chat protection
            
            return packet;
        }
    }
}
