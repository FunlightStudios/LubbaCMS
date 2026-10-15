using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Cache.Type;
using Plus.HabboHotel.Rooms;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Rooms.Settings
{
    /// <summary>
    /// Sends the list of banned users in a room
    /// Header: 1869 (ROOM_BAN_LIST / BannedUsersFromRoomEvent) - used by Nitro
    /// Format: [roomId:int][count:int]{[userId:int][username:string]}
    /// </summary>
    public class NitroBannedUsersComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1869);

            packet.WriteInteger(room.RoomId);

            var banned = room.GetBans().BannedUsers().ToList();
            packet.WriteInteger(banned.Count);

            foreach (int userId in banned)
            {
                UserCache user = PlusEnvironment.GetGame().GetCacheManager().GenerateUser(userId);
                packet.WriteInteger(userId);
                packet.WriteString(user != null ? user.Username : string.Empty);
            }

            return packet;
        }
    }
}
