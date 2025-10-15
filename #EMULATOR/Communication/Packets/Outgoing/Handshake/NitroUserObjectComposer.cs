using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Outgoing.Handshake
{
    /// <summary>
    /// Nitro UserObject Packet
    /// Sends user data to client after authentication
    /// </summary>
    public class NitroUserObjectComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.UserObject);
            
            // User basic info - EXACT NITRO FORMAT
            packet.WriteInteger(user.Id);
            packet.WriteString(user.Username);
            packet.WriteString(user.Look);
            packet.WriteString(user.Gender.ToUpper());
            packet.WriteString(user.Motto);
            packet.WriteString(user.Username); // Real name
            packet.WriteBoolean(false); // Direct mail
            packet.WriteInteger(user.GetStats()?.Respect ?? 0);
            packet.WriteInteger(user.GetStats()?.DailyRespectPoints ?? 10);
            packet.WriteInteger(user.GetStats()?.DailyPetRespectPoints ?? 10);
            packet.WriteBoolean(true); // Stream publishing
            packet.WriteString(user.LastOnline.ToString("dd-MM-yyyy"));
            packet.WriteBoolean(false); // Name change allowed
            packet.WriteBoolean(false); // Account safety locked
            
            return packet;
        }
    }
}
