using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Nitro version of FurnitureListAddOrUpdateComposer with header 104 (USER_FURNITURE_ADD)
    /// Sends one or more furniture items to be added/updated in the inventory without replacing the full list
    /// </summary>
    public class NitroFurniListAddOrUpdateComposer : NitroServerPacket
    {
        public NitroFurniListAddOrUpdateComposer(Item item)
            : base(104) // IncomingHeader.USER_FURNITURE_ADD = 104
        {
            WriteFurnitureItem(this, item);
        }

        private static void WriteFurnitureItem(NitroServerPacket packet, Item item)
        {
            // Format matches FurnitureListItemParser
            packet.WriteInteger(item.Id);                           // itemId
            packet.WriteString(item.IsWallItem ? "I" : "S");       // furniType (S or I)
            packet.WriteInteger(item.BaseItem);                     // ref (base item ID)
            packet.WriteInteger(item.GetBaseItem().SpriteId);       // spriteId
            packet.WriteInteger(1);                                 // category (1 = normal)

            // stuffData (FurnitureDataParser.parseObjectData)
            packet.WriteInteger(0);                                 // data type (0 = legacy)
            packet.WriteString(item.ExtraData ?? "");              // data value

            packet.WriteBoolean(true);                              // isRecyclable
            packet.WriteBoolean(item.GetBaseItem().AllowTrade);    // tradable
            packet.WriteBoolean(true);                              // isGroupable
            packet.WriteBoolean(item.GetBaseItem().AllowMarketplaceSell); // sellable
            packet.WriteInteger(-1);                                // secondsToExpiration (-1 = no expiration)
            packet.WriteBoolean(false);                             // hasRentPeriodStarted
            packet.WriteInteger(-1);                                // flatId (-1 = not in room)

            // Floor items have extra data
            if (!item.IsWallItem)
            {
                packet.WriteString("");                             // slotId (empty)
                packet.WriteInteger(0);                             // extra (unknown)
            }
        }
    }
}
