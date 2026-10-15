using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms.Settings
{
    /// <summary>
    /// Confirms room settings were saved
    /// Header: 948 (ROOM_SETTINGS_SAVE / RoomSettingsSavedEvent)
    /// Format: [roomId:int]
    /// </summary>
    public class NitroRoomSettingsSavedComposer
    {
        public static NitroServerPacket Compose(int roomId)
        {
            var packet = new NitroServerPacket(948);
            packet.WriteInteger(roomId);
            return packet;
        }
    }
}
