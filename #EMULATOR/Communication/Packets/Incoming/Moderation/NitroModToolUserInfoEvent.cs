using Plus.Communication.Packets.Outgoing.Moderation;
using Plus.HabboHotel.GameClients;
using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Incoming.Moderation
{
    /// <summary>
    /// Handles Nitro mod tool user info request
    /// Header: 3295 (MOD_TOOL_USER_INFO)
    /// NOTE: This is handled in WebSocketConnectionHandler for Nitro clients
    /// This class exists only for Flash client compatibility (not used)
    /// </summary>
    class NitroModToolUserInfoEvent : IPacketEvent
    {
        public void Parse(GameClient Session, ClientPacket Packet)
        {
            // This is handled in WebSocketConnectionHandler for Nitro clients
            // Flash clients don't use this packet
            return;
        }
    }
}
