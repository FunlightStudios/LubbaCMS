using System;
using System.Collections.Generic;
using System.Linq;
using log4net;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Catalog;
using Plus.HabboHotel.Items;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends catalog page content with items from database
    /// Header: 804 (CATALOG_PAGE)
    /// </summary>
    public class NitroCatalogPageComposer
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Catalog");
        
        public static NitroServerPacket Compose(CatalogPage page)
        {
            var packet = new NitroServerPacket(804);
            
            // Page ID
            packet.WriteInteger(page.Id);
            
            // Catalog type
            packet.WriteString("NORMAL");
            
            // Layout code
            packet.WriteString(page.Template);
            
            // Images (page_strings_1)
            packet.WriteInteger(page.PageStrings1?.Count ?? 0);
            if (page.PageStrings1 != null)
            {
                foreach (var img in page.PageStrings1)
                {
                    packet.WriteString(img);
                }
            }
            
            // Texts (page_strings_2)
            packet.WriteInteger(page.PageStrings2?.Count ?? 0);
            if (page.PageStrings2 != null)
            {
                foreach (var text in page.PageStrings2)
                {
                    packet.WriteString(text);
                }
            }
            
            var offers = page.Items?.Values.Where(item => item.HaveOffer).ToList() ?? new List<CatalogItem>();

            packet.WriteInteger(offers.Count);

            foreach (var item in offers)
            {
                WriteOffer(packet, item);
            }

            // Default offer ID (Nitro expects a selected offer for templates like 'pets')
            int defaultOfferId = -1;
            if (offers.Count > 0)
            {
                var first = offers.First();
                defaultOfferId = first.OfferId > 0 ? first.OfferId : first.Id;
            }
            packet.WriteInteger(defaultOfferId);
            
            // Accept season currency as credits
            packet.WriteBoolean(false);
            
            return packet;
        }
        
        private static void WriteOffer(NitroServerPacket packet, CatalogItem item)
        {
            int offerId = item.OfferId > 0 ? item.OfferId : item.Id;
            packet.WriteInteger(offerId); // offer ID
            packet.WriteString(item.Data.ItemName); // localization ID / catalog display name
            packet.WriteBoolean(false); // is rent offer
            packet.WriteInteger(item.CostCredits); // price in credits
            // Nitro expects: priceActivityPoints, activityPointType (0=pixels,5=diamonds)
            int priceAP = item.CostDiamonds > 0 ? item.CostDiamonds : item.CostPixels;
            int apType = item.CostDiamonds > 0 ? 5 : 0;
            packet.WriteInteger(priceAP);
            packet.WriteInteger(apType);

            var productType = item.Data.InteractionType == InteractionType.PET ? "p" : item.Data.Type.ToString();
            bool isPet = item.Data.InteractionType == InteractionType.PET;
            packet.WriteBoolean(!isPet); // giftable (pets are not giftable)
            
            // Products in offer
            packet.WriteInteger(1); // number of products in this offer
            
            // Product 1
            packet.WriteString(productType); // product type (s=floor, i=wall, p=pet, e=effect, r=bot, b=badge)

            if (productType == "b")
            {
                packet.WriteString(item.Data.ItemName);
            }
            else
            {
                int classId = isPet ? (item.Data.SpriteId > 0 ? item.Data.SpriteId : item.Data.BehaviourData) : item.Data.SpriteId;
                packet.WriteInteger(classId);

                string extraParam = string.Empty;
                if (item.Data.InteractionType == InteractionType.WALLPAPER || item.Data.InteractionType == InteractionType.FLOOR || item.Data.InteractionType == InteractionType.LANDSCAPE)
                {
                    var parts = item.Name.Split('_');
                    if (parts.Length >= 3) extraParam = parts[2];
                }
                else if (!isPet)
                {
                    extraParam = item.ExtraData ?? string.Empty;
                    if (string.IsNullOrEmpty(extraParam))
                        extraParam = $"classname:{item.Data.ItemName}";
                }

                log.Info($"[CATALOG DEBUG] Item: {item.Data.ItemName}, Type: {item.Data.Type}, SpriteId: {item.Data.SpriteId}, ExtraParam: '{extraParam}'");

                packet.WriteString(extraParam);
                packet.WriteInteger(item.Amount);
                packet.WriteBoolean(item.IsLimited);

                if (item.IsLimited)
                {
                    packet.WriteInteger(item.LimitedEditionStack);
                    packet.WriteInteger(item.LimitedEditionStack - item.LimitedEditionSells);
                }
            }
            
            // Club level required
            packet.WriteInteger(0);
            
            // Bundle purchase allowed
            packet.WriteBoolean(true);
            
            // Is pet
            packet.WriteBoolean(isPet);
            
            // Preview image (absolute or relative URL). Use Nitro icons set shipped with the web.
            var previewUrl = $"/nitro/assets/images/icons/{item.Data.ItemName}_icon.png";
            packet.WriteString(previewUrl);
        }
    }
}
