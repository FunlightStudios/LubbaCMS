using System;
using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Navigator
{
    /// <summary>
    /// Sends can create room response to Nitro client
    /// </summary>
    public class NitroCanCreateRoomComposer
    {
        public static NitroServerPacket Compose(bool hasReachedLimit, int limit)
        {
            var packet = new NitroServerPacket(1435); // CAN_CREATE_ROOM header
            
            packet.WriteInteger(hasReachedLimit ? 1 : 0);
            packet.WriteInteger(limit);
            
            return packet;
        }
    }
}
