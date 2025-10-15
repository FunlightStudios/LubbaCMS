using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Messenger
{
    /// <summary>
    /// Sends messenger/friends initialization to client
    /// </summary>
    public class NitroMessengerInitComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.MessengerInit);
            
            // Max friends
            packet.WriteInteger(300);
            
            // Normal friends max
            packet.WriteInteger(300);
            
            // Extended friends max (VIP)
            packet.WriteInteger(1100);
            
            // Friend categories (empty for now)
            packet.WriteInteger(0);
            
            return packet;
        }
    }
}
