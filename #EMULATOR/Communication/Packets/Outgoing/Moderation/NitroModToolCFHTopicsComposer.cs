using Plus.Communication.Packets.Outgoing.Handshake;
using System.Collections.Generic;

namespace Plus.Communication.Packets.Outgoing.Moderation
{
    /// <summary>
    /// Sends Call for Help topics to Nitro mod tools
    /// Header: 325 (MODERATION_TOPICS)
    /// </summary>
    public class NitroModToolCFHTopicsComposer
    {
        public static NitroServerPacket Compose()
        {
            var packet = new NitroServerPacket(325);
            
            // Categories (only 1 category for now)
            packet.WriteInteger(1); // Number of categories
            
            // Category 1: "General"
            packet.WriteString("General"); // Category name
            
            // Topics in this category
            var topics = new List<CFHTopic>
            {
                new CFHTopic { Id = 1, Name = "Inappropriate behavior" },
                new CFHTopic { Id = 2, Name = "Scamming" },
                new CFHTopic { Id = 3, Name = "Hacking/Cheating" },
                new CFHTopic { Id = 4, Name = "Inappropriate room name" },
                new CFHTopic { Id = 5, Name = "Inappropriate username" }
            };
            
            packet.WriteInteger(topics.Count); // Number of topics
            foreach (var topic in topics)
            {
                packet.WriteString(topic.Name); // Topic name
                packet.WriteInteger(topic.Id); // Topic ID
                packet.WriteString(""); // Unknown string (empty)
            }
            
            return packet;
        }
    }
    
    public class CFHTopic
    {
        public int Id { get; set; }
        public string Name { get; set; }
    }
}
