using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Inventory
{
    /// <summary>
    /// Nitro version of FurniListUpdateComposer with header 3151
    /// Tells the client to refresh the inventory
    /// </summary>
    public class NitroFurniListUpdateComposer : NitroServerPacket
    {
        public NitroFurniListUpdateComposer()
            : base(3151) // ServerPacketHeader.NitroFurniListUpdateMessageComposer = 3151
        {
            // Empty packet - just triggers inventory refresh
        }
    }
}