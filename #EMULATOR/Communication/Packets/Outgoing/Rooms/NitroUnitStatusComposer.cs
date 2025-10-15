using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Users;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends unit status updates (movement, position, actions)
    /// </summary>
    public class NitroUnitStatusComposer
    {
        public static NitroServerPacket Compose(int virtualId, int x, int y, double z, int headRotation, int bodyRotation, string actions = "")
        {
            var packet = new NitroServerPacket(1640); // UNIT_STATUS header
            
            // Number of units
            packet.WriteInteger(1);
            
            // Unit ID (virtual ID in room)
            packet.WriteInteger(virtualId);
            
            // Position
            packet.WriteInteger(x);
            packet.WriteInteger(y);
            packet.WriteString(z.ToString("0.00"));
            
            // Head direction (0-7)
            packet.WriteInteger(headRotation);
            
            // Body direction (0-7)
            packet.WriteInteger(bodyRotation);
            
            // Actions string (e.g., "mv 5,10,0.0/" for movement)
            packet.WriteString(actions);
            
            return packet;
        }
    }
}
