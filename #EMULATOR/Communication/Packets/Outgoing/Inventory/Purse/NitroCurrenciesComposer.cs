using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Outgoing.Inventory.Purse
{
    /// <summary>
    /// Sends all currencies (Credits, Duckets, Diamonds) to Nitro client
    /// </summary>
    public class NitroCurrenciesComposer
    {
        /// <summary>
        /// Compose Credits packet
        /// Header: 3475 (UserCreditsEvent)
        /// </summary>
        public static NitroServerPacket ComposeCredits(Habbo habbo)
        {
            var packet = new NitroServerPacket(3475);
            packet.WriteString(habbo.Credits + ".0");
            return packet;
        }

        /// <summary>
        /// Compose Activity Points (Duckets, Diamonds, etc.)
        /// Header: 2018 (UserCurrencyEvent)
        /// </summary>
        public static NitroServerPacket ComposeActivityPoints(Habbo habbo)
        {
            var packet = new NitroServerPacket(2018);
            
            // Number of currency types
            packet.WriteInteger(3);
            
            // Type 0: Duckets (Pixels)
            packet.WriteInteger(0);
            packet.WriteInteger(habbo.Duckets);
            
            // Type 5: Diamonds
            packet.WriteInteger(5);
            packet.WriteInteger(habbo.Diamonds);
            
            // Type 103: GOTW Points (Game of the Week)
            packet.WriteInteger(103);
            packet.WriteInteger(habbo.GOTWPoints);
            
            return packet;
        }
    }
}
