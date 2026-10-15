using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Broadcasts room user figure/info update to all users in the room
    /// Header: 3920 (UNIT_INFO / RoomUnitInfoEvent)
    /// Format: [unitId:int][figure:string][gender:string][motto:string][achievementScore:int]
    /// </summary>
    public class NitroRoomUnitInfoComposer
    {
        public static NitroServerPacket Compose(RoomUser user, bool self)
        {
            var packet = new NitroServerPacket(3920);

            // unitId: actual room index (virtualId). The client looks up room users by index.
            packet.WriteInteger(user.VirtualId);

            var habbo = user.GetClient().GetHabbo();
            packet.WriteString(habbo.Look);
            packet.WriteString(habbo.Gender.ToUpper());
            packet.WriteString(habbo.Motto);
            packet.WriteInteger(habbo.GetStats().AchievementPoints);

            return packet;
        }
    }
}
