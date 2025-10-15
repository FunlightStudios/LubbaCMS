using Plus.HabboHotel.Users;
using Plus.Communication.Packets.Outgoing.Handshake;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends furniture inventory to client
    /// </summary>
    public class NitroFurniListComposer
    {
        public static NitroServerPacket Compose(Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.FurniList);
            
            var inventory = user.GetInventoryComponent();
            
            if (inventory != null)
            {
                var items = inventory.GetFloorItems().ToList();
                var wallItems = inventory.GetWallItems().ToList();
                int totalItems = items.Count + wallItems.Count;
                
                // Total pages (1 page = 1000 items)
                int totalPages = (totalItems / 1000) + 1;
                packet.WriteInteger(totalPages);
                
                // Current page
                packet.WriteInteger(0);
                
                // Total items
                packet.WriteInteger(totalItems);
                
                // Floor items
                foreach (var item in items)
                {
                    packet.WriteInteger(item.Id);
                    packet.WriteString("S"); // S = Floor item
                    packet.WriteInteger(item.BaseItem);
                    packet.WriteInteger(item.GetBaseItem().SpriteId);
                    packet.WriteString(item.ExtraData);
                    packet.WriteBoolean(true);
                    packet.WriteBoolean(item.GetBaseItem().AllowTrade);
                    packet.WriteBoolean(item.GetBaseItem().AllowMarketplaceSell);
                }
                
                // Wall items
                foreach (var item in wallItems)
                {
                    packet.WriteInteger(item.Id);
                    packet.WriteString("I"); // I = Wall item
                    packet.WriteInteger(item.BaseItem);
                    packet.WriteInteger(item.GetBaseItem().SpriteId);
                    packet.WriteString(item.ExtraData);
                    packet.WriteBoolean(true);
                    packet.WriteBoolean(item.GetBaseItem().AllowTrade);
                    packet.WriteBoolean(item.GetBaseItem().AllowMarketplaceSell);
                }
            }
            else
            {
                // No inventory loaded
                packet.WriteInteger(1);
                packet.WriteInteger(0);
                packet.WriteInteger(0);
            }
            
            return packet;
        }
    }
}
