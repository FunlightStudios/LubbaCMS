using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Rooms;
using System.Collections.Generic;
using System.Linq;

namespace Plus.Communication.Packets.Outgoing.Navigator
{
    /// <summary>
    /// Sends navigator search results (room list) to client
    /// </summary>
    public class NitroNavigatorSearchResultsComposer
    {
        public static NitroServerPacket Compose(string searchQuery)
        {
            return Compose(searchQuery, null);
        }
        
        public static NitroServerPacket Compose(string searchQuery, Plus.HabboHotel.Users.Habbo user)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorSearchResults);
            
            var popularRooms = new List<RoomData>();
            var myRooms = new List<RoomData>();
            
            // Load popular rooms from database
            using (var dbClient = PlusEnvironment.GetDatabaseManager().GetQueryReactor())
            {
                dbClient.SetQuery("SELECT * FROM `rooms` WHERE `roomtype` = 'private' ORDER BY `users_now` DESC LIMIT 10");
                var table = dbClient.GetTable();
                
                if (table != null)
                {
                    foreach (System.Data.DataRow row in table.Rows)
                    {
                        var roomData = new RoomData();
                        roomData.Fill(row);
                        popularRooms.Add(roomData);
                    }
                }
                
                // Load user's rooms if user is provided
                if (user != null)
                {
                    dbClient.SetQuery("SELECT * FROM `rooms` WHERE `owner` = @userId AND `roomtype` = 'private' ORDER BY `id` DESC LIMIT 50");
                    dbClient.AddParameter("userId", user.Id);
                    var userTable = dbClient.GetTable();
                    
                    if (userTable != null)
                    {
                        foreach (System.Data.DataRow row in userTable.Rows)
                        {
                            var roomData = new RoomData();
                            roomData.Fill(row);
                            myRooms.Add(roomData);
                        }
                    }
                }
            }
            
            // NITRO NAVIGATOR FORMAT - CORRECT (from reverse engineering)
            packet.WriteString("popular"); // Search code
            packet.WriteString(""); // Filter text
            
            // Number of result blocks (My Rooms + Popular Rooms)
            int blockCount = 1; // Always show Popular Rooms
            if (myRooms.Count > 0) blockCount++; // Add My Rooms if user has rooms
            
            packet.WriteInteger(blockCount);
            
            // MY ROOMS BLOCK (if user has rooms)
            if (myRooms.Count > 0)
            {
                packet.WriteString("myrooms"); // Block code
                packet.WriteString("My Rooms"); // Block text
                packet.WriteInteger(0); // Action allowed
                packet.WriteBoolean(false); // Force closed
                packet.WriteInteger(0); // View mode
                
                // Room count in this block
                packet.WriteInteger(myRooms.Count);
                
                foreach (var room in myRooms)
                {
                    WriteRoomData(packet, room);
                }
            }
            
            // POPULAR ROOMS BLOCK
            packet.WriteString("popular"); // Block code
            packet.WriteString("Popular Rooms"); // Block text
            packet.WriteInteger(0); // Action allowed
            packet.WriteBoolean(false); // Force closed
            packet.WriteInteger(0); // View mode
            
            // Room count in this block
            packet.WriteInteger(popularRooms.Count);
            
            foreach (var room in popularRooms)
            {
                WriteRoomData(packet, room);
            }
            
            return packet;
        }
        
        private static void WriteRoomData(NitroServerPacket packet, RoomData room)
        {
            packet.WriteInteger(room.Id);
            packet.WriteString(room.Name);
            packet.WriteInteger(room.OwnerId);
            packet.WriteString(room.OwnerName);
            packet.WriteInteger((int)room.Access);
            packet.WriteInteger(room.UsersNow);
            packet.WriteInteger(room.UsersMax);
            packet.WriteString(room.Description ?? "");
            packet.WriteInteger(0); // Trade mode
            packet.WriteInteger(room.Score);
            packet.WriteInteger(0); // Ranking
            packet.WriteInteger(room.Category);
            
            // Tags
            var tags = room.Tags?.ToList() ?? new List<string>();
            packet.WriteInteger(tags.Count);
            foreach (var tag in tags)
            {
                packet.WriteString(tag);
            }
            
            // BitMask for optional data
            // Bit 1 (1): THUMBNAIL
            // Bit 2 (2): GROUPDATA
            // Bit 3 (4): ROOMAD
            // Bit 4 (8): SHOWOWNER
            // Bit 5 (16): ALLOW_PETS
            // Bit 6 (32): DISPLAY_ROOMAD
            int bitMask = 0;
            if (true) bitMask |= 8; // Show owner
            if (room.AllowPets == 1) bitMask |= 16; // Allow pets
            
            packet.WriteInteger(bitMask);
            
            // No optional data (thumbnail, group, ad) since bitMask doesn't include them
        }
    }
}
