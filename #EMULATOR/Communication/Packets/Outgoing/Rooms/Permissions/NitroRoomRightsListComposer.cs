using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Cache;
using Plus.HabboHotel.Cache.Type;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms.Permissions
{
    /// <summary>
    /// Sends the list of users who have rights in a room
    /// Header: 1284 (ROOM_RIGHTS_LIST / FlatControllersEvent) - used by Nitro
    /// Format: [roomId:int][count:int]{[userId:int][username:string]}
    /// </summary>
    public class NitroRoomRightsListComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1284);

            packet.WriteInteger(room.RoomId);
            packet.WriteInteger(room.UsersWithRights.Count);

            foreach (int userId in room.UsersWithRights)
            {
                UserCache user = PlusEnvironment.GetGame().GetCacheManager().GenerateUser(userId);
                packet.WriteInteger(userId);
                packet.WriteString(user != null ? user.Username : string.Empty);
            }

            return packet;
        }
    }
}
