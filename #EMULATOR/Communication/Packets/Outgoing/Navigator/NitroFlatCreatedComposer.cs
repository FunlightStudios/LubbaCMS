using System;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Navigator
{
    /// <summary>
    /// Sends room created confirmation to Nitro client
    /// </summary>
    public class NitroFlatCreatedComposer
    {
        public static NitroServerPacket Compose(int roomId, string roomName)
        {
            var packet = new NitroServerPacket(2064); // FLAT_CREATED header
            
            packet.WriteInteger(roomId);
            packet.WriteString(roomName);
            
            return packet;
        }
    }
}
