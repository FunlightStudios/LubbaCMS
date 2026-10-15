using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using System;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Sends bot to Nitro client with correct Type=3
    /// </summary>
    public class NitroBotComposer
    {
        public static NitroServerPacket Compose(RoomUser bot)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.Unit);
            
            // User count
            packet.WriteInteger(1);
            
            // Bot data
            // CRITICAL: Bot ID must be NEGATIVE to avoid collision with user IDs!
            // Nitro sets webID = (roomIndex * -1) for bots
            packet.WriteInteger(bot.BotData.Id * -1); // Bot ID (negative!)
            packet.WriteString(bot.BotData.Name); // Bot name
            packet.WriteString(bot.BotData.Motto); // Bot motto
            packet.WriteString(bot.BotData.Look); // Bot figure
            packet.WriteInteger(bot.VirtualId); // Room index
            packet.WriteInteger(bot.X); // X
            packet.WriteInteger(bot.Y); // Y
            packet.WriteString(bot.Z.ToString("0.00")); // Z height
            packet.WriteInteger(bot.RotBody); // Direction (0-7)
            packet.WriteInteger(3); // Type: 1 = user, 2 = pet, 3 = bot (CRITICAL!)
            
            // Type 3 (Bot) has NO additional data!
            // Nitro only reads: ID, Name, Motto, Figure, RoomIndex, X, Y, Z, Direction, Type
            // That's it! No owner, no actions, nothing else!
            
            return packet;
        }
    }
}
