using Plus.Communication.Packets.Outgoing.Handshake;
using System.Collections.Generic;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends mod tool initialization data to Nitro client
    /// Header: 2696 (MODERATION_TOOL)
    /// </summary>
    public class NitroModToolInitComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(2696);
            
            // Issues/Tickets (empty for now)
            packet.WriteInteger(0); // Ticket count
            
            // Message presets
            var messagePresets = new List<string>
            {
                "Please respect other users",
                "Please do not spam",
                "Please do not scam",
                "Please follow the hotel rules"
            };
            
            packet.WriteInteger(messagePresets.Count);
            foreach (var preset in messagePresets)
            {
                packet.WriteString(preset);
            }
            
            // Unknown strings array (empty)
            packet.WriteInteger(0);
            
            // Permissions (all true for moderators)
            packet.WriteBoolean(true); // Unknown permission 1
            packet.WriteBoolean(true); // Unknown permission 2
            packet.WriteBoolean(true); // Can alert (_Str_18465)
            packet.WriteBoolean(true); // Can kick (_Str_20397)
            packet.WriteBoolean(true); // Can ban (_Str_21242)
            packet.WriteBoolean(true); // Unknown permission 3
            packet.WriteBoolean(true); // Unknown permission 4
            
            // More strings array (empty)
            packet.WriteInteger(0);
            
            return packet;
        }
    }
}
