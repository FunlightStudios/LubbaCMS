using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Catalog;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends a single catalog offer (Nitro format)
    /// Header: NitroPacketHeaders.CatalogOffer (2347)
    /// </summary>
    public static class NitroCatalogOfferComposer
    {
        public static NitroServerPacket Compose(CatalogItem item)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.CatalogOffer);

            // Offer header
            int offerId = item.OfferId > 0 ? item.OfferId : item.Id;
            packet.WriteInteger(offerId); // offer ID (prefer offerId)
            packet.WriteString(item.Data.ItemName); // localization ID / name used by Nitro
            packet.WriteBoolean(false); // is rent offer
            packet.WriteInteger(item.CostCredits); // price in credits

            // Activity points (price + type)
            int priceAP = item.CostDiamonds > 0 ? item.CostDiamonds : item.CostPixels;
            int apType = item.CostDiamonds > 0 ? 5 : 0;
            packet.WriteInteger(priceAP);
            packet.WriteInteger(apType);

            var isPet = item.Data.InteractionType == InteractionType.PET;
            packet.WriteBoolean(!isPet); // giftable (pets not giftable)

            // Products
            packet.WriteInteger(1);
            var productType = isPet ? "p" : item.Data.Type.ToString();
            packet.WriteString(productType);

            int classId = isPet ? item.Data.BehaviourData : item.Data.SpriteId;
            packet.WriteInteger(classId);

            string extraParam = string.Empty;
            if (!isPet)
            {
                extraParam = item.ExtraData ?? string.Empty;
                if (string.IsNullOrEmpty(extraParam))
                    extraParam = $"classname:{item.Data.ItemName}";
            }
            packet.WriteString(extraParam);

            packet.WriteInteger(item.Amount); // product count
            packet.WriteBoolean(item.IsLimited);
            if (item.IsLimited)
            {
                packet.WriteInteger(item.LimitedEditionStack);
                packet.WriteInteger(item.LimitedEditionStack - item.LimitedEditionSells);
            }

            // Club level
            packet.WriteInteger(0);
            // Bundle allowed
            packet.WriteBoolean(true);
            // Is pet
            packet.WriteBoolean(isPet);

            // Preview image
            var previewUrl = $"/nitro/assets/images/icons/{item.Data.ItemName}_icon.png";
            packet.WriteString(previewUrl);

            return packet;
        }
    }
}
