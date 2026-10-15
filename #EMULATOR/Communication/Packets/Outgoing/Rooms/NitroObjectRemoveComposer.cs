using Plus.Communication.Packets.Outgoing.Handshake;

namespace Plus.Communication.Packets.Outgoing.Rooms
{
    /// <summary>
    /// Removes a single floor item from the room
    /// Header: 94 (OBJECT_REMOVE)
    /// </summary>
    public class NitroObjectRemoveComposer
    {
        public static NitroServerPacket Compose(int itemId, int userId)
        {
            // FURNITURE_FLOOR_REMOVE from Nitro client IncomingHeader
            // Format from FurnitureFloorRemoveParser:
            // 1. itemId (STRING!)
            // 2. isExpired (Boolean) - false = picked up by user, true = expired/removed by system
            // 3. userId (Integer)
            // 4. delay (Integer) - 0 = instant
            var packet = new NitroServerPacket(2703);
            
            // Item ID as STRING (Nitro parses it with parseInt(wrapper.readString()))
            packet.WriteString(itemId.ToString());
            
            // isExpired = false (item was picked up, not expired)
            packet.WriteBoolean(false);
            
            // User ID who picked it up
            packet.WriteInteger(userId);
            
            // Delay (0 = instant removal)
            packet.WriteInteger(0);
            
            return packet;
        }
    }
}
