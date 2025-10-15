using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Sends pet inventory to client
    /// </summary>
    public class NitroPetInventoryComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.PetInventory);
            
            // Total pets
            packet.WriteInteger(0);
            
            // Pets array (empty for now)
            
            return packet;
        }
    }
}
