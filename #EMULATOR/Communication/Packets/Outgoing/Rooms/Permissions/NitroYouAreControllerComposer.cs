using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms.Permissions
{
    /// <summary>
    /// Tells a user their room controller level
    /// Header: 780 (ROOM_RIGHTS / RoomRightsEvent) - used by Nitro
    /// Format: [controllerLevel:int] (0 = none, 1 = controller, 2 = group admin?, 3 = group owner, 4 = room owner)
    /// </summary>
    public class NitroYouAreControllerComposer
    {
        public static NitroServerPacket Compose(int controllerLevel)
        {
            var packet = new NitroServerPacket(780);
            packet.WriteInteger(controllerLevel);
            return packet;
        }
    }
}
