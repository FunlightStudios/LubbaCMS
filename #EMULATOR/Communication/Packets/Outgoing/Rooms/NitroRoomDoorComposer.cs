using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room door position
    /// </summary>
    public class NitroRoomDoorComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomModelDoor);
            
            if (room != null && room.GetGameMap() != null)
            {
                var model = room.GetGameMap().Model;
                
                // Door X position
                packet.WriteInteger(model.DoorX);
                
                // Door Y position
                packet.WriteInteger(model.DoorY);
                
                // Door direction (rotation)
                packet.WriteInteger(model.DoorOrientation);
            }
            else
            {
                // Default door position
                packet.WriteInteger(0);
                packet.WriteInteger(0);
                packet.WriteInteger(2);
            }
            
            return packet;
        }
    }
}
