using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.Database.Interfaces;
using Plus.HabboHotel.Users;
using System;
using System.Data;

namespace Plus.Communication.Packets.Outgoing.Avatar
{
    /// <summary>
    /// Sends wardrobe outfits to Nitro client
    /// Header: 3315 (USER_OUTFITS / WardrobeMessageEvent)
    /// Format: [pageId:int][outfitCount:int] foreach: [slotId:int][look:string][gender:string]
    /// </summary>
    public class NitroWardrobeComposer
    {
        public static NitroServerPacket Compose(Habbo habbo, int pageId = 0)
        {
            var packet = new NitroServerPacket(3315);

            packet.WriteInteger(pageId);

            using (var dbClient = PlusEnvironment.GetDatabaseManager().GetQueryReactor())
            {
                dbClient.SetQuery("SELECT `slot_id`,`look`,`gender` FROM `user_wardrobe` WHERE `user_id` = @userId");
                dbClient.AddParameter("userId", habbo.Id);
                DataTable wardrobeData = dbClient.GetTable();

                if (wardrobeData == null)
                {
                    packet.WriteInteger(0);
                }
                else
                {
                    packet.WriteInteger(wardrobeData.Rows.Count);
                    foreach (DataRow row in wardrobeData.Rows)
                    {
                        packet.WriteInteger(Convert.ToInt32(row["slot_id"]));
                        packet.WriteString(Convert.ToString(row["look"]));
                        packet.WriteString(Convert.ToString(row["gender"]).ToUpper());
                    }
                }
            }

            return packet;
        }
    }
}
