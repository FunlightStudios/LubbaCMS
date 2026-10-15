using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using System.Collections.Generic;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Rooms.Settings
{
    /// <summary>
    /// Sends room settings data to Nitro client
    /// Header: 1498 (ROOM_SETTINGS / RoomSettingsDataEvent)
    /// Format matches RoomSettingsDataParser
    /// </summary>
    public class NitroRoomSettingsDataComposer
    {
        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1498);

            packet.WriteInteger(room.RoomId);
            packet.WriteString(room.Name);
            packet.WriteString(room.Description);
            packet.WriteInteger((int)room.Access);
            packet.WriteInteger(room.Category);
            packet.WriteInteger(room.UsersMax);
            // Client uses maximumVisitorsLimit as the current max-visitors dropdown value
            packet.WriteInteger(room.UsersMax);

            var tags = new List<string>(room.Tags);
            packet.WriteInteger(tags.Count);
            foreach (var tag in tags)
                packet.WriteString(tag);

            packet.WriteInteger(room.TradeSettings);
            packet.WriteInteger(room.AllowPets);
            packet.WriteInteger(room.AllowPetsEating);
            // RoomBlockingEnabled maps to DB room_blocking_disabled: 1 = walkthrough allowed
            packet.WriteInteger(room.RoomBlockingEnabled);
            packet.WriteInteger(room.Hidewall);
            packet.WriteInteger(room.WallThickness);
            packet.WriteInteger(room.FloorThickness);

            // Chat settings
            packet.WriteInteger(room.chatMode);
            packet.WriteInteger(room.chatSize);
            packet.WriteInteger(room.chatSpeed);
            packet.WriteInteger(room.chatDistance);
            packet.WriteInteger(room.extraFlood);

            packet.WriteBoolean(true); // allowNavigatorDynamicCats

            // Moderation settings
            packet.WriteInteger(room.WhoCanMute);
            packet.WriteInteger(room.WhoCanKick);
            packet.WriteInteger(room.WhoCanBan);

            return packet;
        }
    }
}
