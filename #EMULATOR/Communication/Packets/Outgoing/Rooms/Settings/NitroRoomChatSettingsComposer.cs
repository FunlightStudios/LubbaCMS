using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;

namespace Plus.Communication.Packets.Outgoing.Rooms.Settings
{
    /// <summary>
    /// Nitro ROOM_SETTINGS_CHAT packet (Header 1191)
    /// Updates the chat widget's live room chat settings.
    /// </summary>
    public class NitroRoomChatSettingsComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1191);
            packet.WriteInteger(room != null ? room.chatMode : 0);
            packet.WriteInteger(room != null ? room.chatSize : 1);
            packet.WriteInteger(room != null ? room.chatSpeed : 1);
            packet.WriteInteger(room != null ? room.chatDistance : 14);
            packet.WriteInteger(room != null ? room.extraFlood : 0);
            return packet;
        }
    }
}
