using Plus.Communication.WebSocket;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Outgoing.Users
{
    /// <summary>
    /// Sends updated user data (including daily respects)
    /// Header: 2725 (USER_DATA)
    /// </summary>
    public class NitroUserDataComposer
    {
        public static NitroServerPacket Compose(Habbo habbo)
        {
            var packet = new NitroServerPacket(2725); // USER_DATA
            packet.WriteInteger(habbo.Id);
            packet.WriteString(habbo.Username);
            packet.WriteString(habbo.Look);
            packet.WriteString(habbo.Gender.ToUpper());
            packet.WriteString(habbo.Motto);
            packet.WriteString(""); // Real name (empty)
            packet.WriteBoolean(false); // Stream publishing allowed
            packet.WriteInteger(habbo.GetStats().Respect); // Respect
            packet.WriteInteger(habbo.GetStats().DailyRespectPoints); // Daily respect points
            packet.WriteInteger(habbo.GetStats().DailyPetRespectPoints); // Daily pet respect points
            packet.WriteBoolean(true); // Can change name
            return packet;
        }
    }
}
