using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Messenger
{
    /// <summary>
    /// Sends friend list to client
    /// </summary>
    public class NitroFriendListUpdateComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.FriendListUpdate);
            
            var messenger = user.GetMessenger();
            
            if (messenger != null)
            {
                var friends = messenger.GetFriends().ToList();
                
                // Total friends
                packet.WriteInteger(friends.Count);
                
                // Friends array
                foreach (var friend in friends)
                {
                    packet.WriteInteger(friend.Id);
                    packet.WriteString(friend.mUsername);
                    packet.WriteString("M"); // Default gender
                    packet.WriteBoolean(friend.IsOnline);
                    packet.WriteBoolean(friend.InRoom);
                    packet.WriteString(friend.mLook);
                    packet.WriteString(friend.mMotto);
                    packet.WriteInteger(0);
                }
            }
            else
            {
                // No messenger
                packet.WriteInteger(0);
            }
            
            return packet;
        }
    }
}
