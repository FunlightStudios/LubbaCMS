using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends bot inventory to client
    /// </summary>
    public class NitroBotInventoryComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.BotInventory);
            
            // Total bots
            packet.WriteInteger(0);
            
            // Bots array (empty for now)
            
            return packet;
        }
    }
}
