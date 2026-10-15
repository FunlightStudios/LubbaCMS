using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends room info to Nitro mod tools
    /// Header: 1333 (MODTOOL_ROOM_INFO)
    /// </summary>
    public class NitroModToolRoomInfoComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1333);
            
            if (room == null)
            {
                packet.WriteInteger(0); // Room ID
                packet.WriteInteger(0); // User count
                packet.WriteBoolean(false); // Owner in room
                packet.WriteInteger(0); // Owner ID
                packet.WriteString("Unknown"); // Owner name
                packet.WriteString("Unknown"); // Room name
                packet.WriteString(""); // Description
                packet.WriteInteger(0); // Tags count
                packet.WriteBoolean(false); // Public room
                return packet;
            }
            
            // Room basic info
            packet.WriteInteger(room.Id); // Room ID
            packet.WriteInteger(room.UserCount); // Current user count
            packet.WriteBoolean(room.GetRoomUserManager().GetRoomUserByHabbo(room.OwnerName) != null); // Owner in room
            packet.WriteInteger(room.OwnerId); // Owner ID
            packet.WriteString(room.OwnerName); // Owner name
            
            // Room details
            packet.WriteString(room.Name); // Room name
            packet.WriteString(room.Description); // Description
            
            // Tags
            packet.WriteInteger(room.Tags.Count);
            foreach (string tag in room.Tags)
            {
                packet.WriteString(tag);
            }
            
            // Room type
            packet.WriteBoolean(room.RoomData.Type == "public"); // Is public room
            
            return packet;
        }
    }
}
