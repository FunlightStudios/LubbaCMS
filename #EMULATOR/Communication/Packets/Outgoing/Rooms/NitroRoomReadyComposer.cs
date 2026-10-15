using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Notifies client that room is ready for interaction
    /// Header: 1304 (ROOM_CREATED)
    /// </summary>
    public class NitroRoomReadyComposer
    {
        public static NitroServerPacket Compose(string modelName, int roomId)
        {
            // Header 1304 - Send EMPTY payload for Nitro compatibility
            // Client is misinterpreting ANY payload as a room forward command
            var packet = new NitroServerPacket(1304);
            
            // NO payload - just the header
            
            return packet;
        }
    }
}
