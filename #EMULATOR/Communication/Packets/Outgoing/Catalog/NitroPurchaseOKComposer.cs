using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Catalog;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends the Nitro PurchaseOK packet.
    /// Header: 869 (CATALOG_PURCHASE_OK)
    /// </summary>
    public class NitroPurchaseOKComposer : NitroServerPacket
    {
        public NitroPurchaseOKComposer(CatalogItem item, ItemData baseItem)
            : base(869)
        {
            bool isPet = baseItem.InteractionType == InteractionType.PET;

            WriteInteger(item.OfferId > 0 ? item.OfferId : item.Id);
            WriteString(baseItem.ItemName);
            WriteBoolean(false); // rent
            WriteInteger(item.CostCredits);
            WriteInteger(item.CostDiamonds > 0 ? item.CostDiamonds : item.CostPixels);
            WriteInteger(item.CostDiamonds > 0 ? 5 : 0);
            WriteBoolean(!isPet); // giftable

            WriteInteger(1); // product count
            string productType = isPet ? "p" : baseItem.Type.ToString();
            WriteString(productType);

            if (productType == "b")
            {
                WriteString(baseItem.ItemName);
            }
            else
            {
                WriteInteger(isPet ? baseItem.BehaviourData : baseItem.SpriteId);
                WriteString(item.ExtraData ?? string.Empty);
                WriteInteger(item.Amount);
                WriteBoolean(item.IsLimited);

                if (item.IsLimited)
                {
                    WriteInteger(item.LimitedEditionStack);
                    WriteInteger(item.LimitedEditionStack - item.LimitedEditionSells);
                }
            }

            WriteInteger(0); // club level
            WriteBoolean(true); // bundle purchase allowed
        }
    }
}
