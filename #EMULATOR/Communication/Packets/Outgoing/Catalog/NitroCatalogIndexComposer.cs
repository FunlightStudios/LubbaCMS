using Plus.Communication.Packets.Outgoing.Handshake;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends catalog index (categories) to client
    /// </summary>
    public class NitroCatalogIndexComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.CatalogIndex);
            
            var catalog = PlusEnvironment.GetGame().GetCatalog();
            
            if (catalog != null)
            {
                var pages = catalog.GetPages().Where(x => x.Visible && x.ParentId == -1).ToList();
                
                // Root node
                packet.WriteBoolean(true);
                
                // Categories count
                packet.WriteInteger(pages.Count);
                
                // Categories array
                foreach (var page in pages)
                {
                    packet.WriteInteger(page.Id);
                    packet.WriteString(page.Caption);
                    packet.WriteString(page.PageLink); // Page link as caption
                    packet.WriteBoolean(page.Visible);
                    packet.WriteBoolean(page.Enabled);
                    packet.WriteString(page.Icon.ToString());
                    packet.WriteInteger(page.MinimumRank);
                    
                    // Sub-pages count
                    var subPages = catalog.GetPages().Where(x => x.ParentId == page.Id).ToList();
                    packet.WriteInteger(subPages.Count);
                }
            }
            else
            {
                // No catalog loaded
                packet.WriteBoolean(true);
                packet.WriteInteger(0);
            }
            
            return packet;
        }
    }
}
