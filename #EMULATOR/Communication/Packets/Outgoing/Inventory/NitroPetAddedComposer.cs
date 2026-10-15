using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms.AI;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends a newly added pet to the Nitro inventory.
    /// Header: 2101 (USER_PET_ADD)
    /// </summary>
    public class NitroPetAddedComposer : NitroServerPacket
    {
        public NitroPetAddedComposer(Pet pet, bool boughtAsGift)
            : base(2101)
        {
            NitroPetInventoryComposer.WritePetData(this, pet);
            WriteBoolean(boughtAsGift);
        }
    }
}
