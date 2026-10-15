using System.Collections.Generic;
using System.Linq;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;
using Plus.HabboHotel.Rooms;
using log4net;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends all wall items in a room
    /// Header: 1369 (ITEM_WALL)
    /// </summary>
    public class NitroWallItemsComposer
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.Packets.Outgoing.Rooms");

        public static NitroServerPacket Compose(Room room)
        {
            var packet = new NitroServerPacket(1369); // ITEM_WALL header

            if (room == null || room.GetRoomItemHandler() == null)
            {
                packet.WriteInteger(0); // Owner count
                packet.WriteInteger(0); // Item count
                return packet;
            }

            var itemHandler = room.GetRoomItemHandler();
            var wallItems = itemHandler.GetWall.ToList();

            // Collect unique owners
            var owners = new Dictionary<int, string>();
            foreach (var item in wallItems)
            {
                if (item == null || item.UserID == 0) continue;

                if (!owners.ContainsKey(item.UserID))
                {
                    var habbo = PlusEnvironment.GetHabboById(item.UserID);
                    owners[item.UserID] = habbo?.Username ?? "Unknown";
                }
            }

            // Write owners
            packet.WriteInteger(owners.Count);
            foreach (var owner in owners)
            {
                packet.WriteInteger(owner.Key);    // User ID
                packet.WriteString(owner.Value);   // Username
            }

            // Write items
            packet.WriteInteger(wallItems.Count);

            foreach (var item in wallItems)
            {
                if (item == null) continue;

                WriteWallItem(packet, item);
            }

            log.Debug($"Composed wall items packet: {owners.Count} owners, {wallItems.Count} items");

            return packet;
        }

        private static void WriteWallItem(NitroServerPacket packet, Item item)
        {
            // Item ID as string
            packet.WriteString(item.Id.ToString());

            // Sprite ID (base item ID)
            packet.WriteInteger(item.GetBaseItem().SpriteId);

            // Wall location
            packet.WriteString(item.wallCoord ?? "");

            // Stuff data (state)
            packet.WriteString(item.ExtraData ?? "0");

            // Expires (-1 = never)
            packet.WriteInteger(-1);

            // Usage policy
            packet.WriteInteger(item.GetBaseItem().InteractionType == HabboHotel.Items.InteractionType.NONE ? 0 : 1);

            // Owner user ID
            packet.WriteInteger(item.UserID);
        }
    }
}
