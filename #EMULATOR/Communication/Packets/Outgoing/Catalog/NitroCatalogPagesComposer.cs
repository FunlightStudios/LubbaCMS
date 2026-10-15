using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Catalog;
using Plus.HabboHotel.Users;
using System.Collections.Generic;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends catalog page structure from database
    /// Header: 1032 (CATALOG_PAGES)
    /// </summary>
    public class NitroCatalogPagesComposer
    {
        public static NitroServerPacket Compose(Habbo habbo)
        {
            var packet = new NitroServerPacket(1032);
            
            // Get pages from catalog manager
            var pages = PlusEnvironment.GetGame().GetCatalog().GetPages();
            
            // Write root page with children from DB
            WriteCatalogPageTree(packet, pages, habbo);
            
            // New additions available
            packet.WriteBoolean(false);
            
            // Catalog type
            packet.WriteString("NORMAL");
            
            return packet;
        }
        
        private static void WriteCatalogPageTree(NitroServerPacket packet, ICollection<CatalogPage> pages, Habbo habbo)
        {
            // Root page
            packet.WriteBoolean(true); // visible
            packet.WriteInteger(0); // icon
            packet.WriteInteger(-1); // pageId (root)
            packet.WriteString("root"); // pageName
            packet.WriteString("root"); // localization
            
            // Offer IDs (empty for root)
            packet.WriteInteger(0);
            
            // Get top-level pages (parent_id = -1)
            var topPages = pages.Where(p => p.ParentId == -1 && AllowedFor(habbo, p)).ToList();
            
            // Children count
            packet.WriteInteger(topPages.Count);
            
            // Write each top-level page
            foreach (var page in topPages)
            {
                WritePage(packet, page, pages, habbo);
            }
        }
        
        private static void WritePage(NitroServerPacket packet, CatalogPage page, ICollection<CatalogPage> allPages, Habbo habbo)
        {
            packet.WriteBoolean(page.Visible); // visible
            packet.WriteInteger(page.Icon); // icon
            // If disabled, write as node (non-clickable) using pageId = -1
            packet.WriteInteger(page.Enabled ? page.Id : -1); // pageId
            packet.WriteString(page.PageLink); // pageName
            packet.WriteString(page.Caption); // localization
            // Include offer IDs as in Flash index (some Nitro UIs expect this link-up)
            var offersCount = page.ItemOffers != null ? page.ItemOffers.Count : 0;
            packet.WriteInteger(offersCount);
            if (offersCount > 0)
            {
                foreach (var offerId in page.ItemOffers.Keys)
                {
                    packet.WriteInteger(offerId);
                }
            }
            
            // Get children of this page
            var children = allPages.Where(p => p.ParentId == page.Id && AllowedFor(habbo, p)).ToList();
            packet.WriteInteger(children.Count);
            
            // Write children recursively
            foreach (var child in children)
            {
                WritePage(packet, child, allPages, habbo);
            }
        }

        private static bool AllowedFor(Habbo habbo, CatalogPage p)
        {
            if (p == null || !p.Visible)
                return false;
            if (habbo == null)
                return p.Visible; // fallback: show visible pages
            if (p.MinimumRank > habbo.Rank)
                return false;
            // VIP gating only applies to normal users (rank == 1). Staff bypass.
            if (p.MinimumVIP > habbo.VIPRank && habbo.Rank == 1)
                return false;
            return true;
        }
    }
}
