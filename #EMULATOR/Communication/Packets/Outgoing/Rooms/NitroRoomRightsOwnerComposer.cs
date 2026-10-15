using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Notifies client that they are the room owner
    /// Header: 339 (ROOM_RIGHTS_OWNER)
    /// </summary>
    public class NitroRoomRightsOwnerComposer
    {
        public static NitroServerPacket Compose()
        {
            // Header 339 = ROOM_RIGHTS_OWNER
            // Empty packet - just notifies client they are owner
            var packet = new NitroServerPacket(339);
            
            return packet;
        }
    }
}
