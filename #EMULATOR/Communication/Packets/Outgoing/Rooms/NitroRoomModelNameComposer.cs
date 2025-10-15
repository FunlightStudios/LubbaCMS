using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends room model name
    /// </summary>
    public class NitroRoomModelNameComposer
    {
        public static NitroServerPacket Compose(string modelName, int roomId)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.RoomModelName);
            
            packet.WriteString(modelName);
            packet.WriteInteger(roomId);
            
            return packet;
        }
    }
}
