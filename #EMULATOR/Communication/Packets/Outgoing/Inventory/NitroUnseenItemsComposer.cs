using System.Collections.Generic;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Marks newly received inventory objects as unseen in Nitro.
    /// Header: 2103 (UNSEEN_ITEMS)
    /// </summary>
    public class NitroUnseenItemsComposer : NitroServerPacket
    {
        public NitroUnseenItemsComposer(Dictionary<int, List<int>> itemsByCategory)
            : base(2103)
        {
            WriteInteger(itemsByCategory?.Count ?? 0);

            if (itemsByCategory == null)
                return;

            foreach (var category in itemsByCategory)
            {
                WriteInteger(category.Key);
                WriteInteger(category.Value.Count);

                foreach (var itemId in category.Value)
                    WriteInteger(itemId);
            }
        }
    }
}
