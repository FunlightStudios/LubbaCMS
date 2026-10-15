using System.Collections.Generic;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Catalog.Pets;

namespace Plus.Communication.Packets.Outgoing.Catalog
{
    /// <summary>
    /// Sends sellable pet palettes to Nitro.
    /// Header: 3331 (CATALOG_RECEIVE_PET_BREEDS)
    /// Format: productCode, count, then per entry: breedId, paletteId, isRare, sellable
    /// </summary>
    public class NitroSellablePetPalettesComposer : NitroServerPacket
    {
        public NitroSellablePetPalettesComposer(string productCode, ICollection<PetRace> races)
            : base(3331)
        {
            WriteString(productCode ?? string.Empty);
            WriteInteger(races?.Count ?? 0);

            if (races == null)
                return;

            foreach (var race in races)
            {
                WriteInteger(race.RaceId); // breedId
                WriteInteger(race.RaceId); // paletteId (same as breedId in our DB)
                WriteBoolean(race.Rare); // isRare
                WriteBoolean(race.Sellable); // sellable
            }
        }
    }
}
