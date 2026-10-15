using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms.Settings
{
    /// <summary>
    /// Notifies the client that room info was updated
    /// Header: 3299 (RoomInfoUpdatedEvent) - used by Nitro
    /// Format: [roomId:int]
    /// </summary>
    public class NitroRoomInfoUpdatedComposer
    {
        public static NitroServerPacket Compose(int roomId)
        {
            var packet = new NitroServerPacket(3297);
            packet.WriteInteger(roomId);
            return packet;
        }
    }
}
