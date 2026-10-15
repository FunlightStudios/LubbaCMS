using System.Collections.Generic;
using System.Linq;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.HabboHotel.Navigator;

namespace Plus.Communication.Packets.Outgoing.Navigator
{
    /// <summary>
    /// Sends navigator metadata, categories and auxiliary navigator lists to Nitro.
    /// </summary>
    public static class NitroNavigatorDataComposer
    {
        public static NitroServerPacket ComposeMetadata()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorMetaData);
            var contexts = PlusEnvironment.GetGame().GetNavigator().GetTopLevelItems()
                .OrderBy(item => item.Id)
                .ToList();

            packet.WriteInteger(contexts.Count);
            foreach (var context in contexts)
            {
                packet.WriteString(context.SearchCode ?? string.Empty);
                packet.WriteInteger(0); // saved searches
            }

            return packet;
        }

        public static NitroServerPacket ComposeLiftedRooms()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorLiftedRooms);
            packet.WriteInteger(0);
            return packet;
        }

        public static NitroServerPacket ComposeCollapsedCategories()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorCollapsedCategories);
            packet.WriteInteger(0);
            return packet;
        }

        public static NitroServerPacket ComposeSavedSearches()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorSearches);
            packet.WriteInteger(0);
            return packet;
        }

        public static NitroServerPacket ComposeSettings()
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorSettings);

            packet.WriteInteger(0);     // windowX
            packet.WriteInteger(0);     // windowY
            packet.WriteInteger(425);   // windowWidth
            packet.WriteInteger(535);   // windowHeight
            packet.WriteBoolean(false); // leftPanelHidden
            packet.WriteInteger(0);     // resultsMode

            return packet;
        }

        public static NitroServerPacket ComposeHomeRoom(int homeRoomId)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorHomeRoom);
            packet.WriteInteger(homeRoomId);
            packet.WriteInteger(homeRoomId);
            return packet;
        }

        public static NitroServerPacket ComposeFlatCategories(int userRank)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorCategories);
            var categories = PlusEnvironment.GetGame().GetNavigator().GetFlatCategories()
                .OrderBy(category => category.OrderId)
                .ToList();

            packet.WriteInteger(categories.Count);
            foreach (var category in categories)
            {
                packet.WriteInteger(category.Id);
                packet.WriteString(category.PublicName ?? string.Empty);
                packet.WriteBoolean(category.RequiredRank <= userRank);
                packet.WriteBoolean(false);
                packet.WriteString(category.CategoryIdentifier ?? string.Empty);
                packet.WriteString(category.Category ?? string.Empty);
                packet.WriteBoolean(category.RequiredRank > userRank);
            }

            return packet;
        }

        public static NitroServerPacket ComposeEventCategories(int userRank)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.NavigatorEventCategories);
            var categories = PlusEnvironment.GetGame().GetNavigator().GetEventCategories()
                .OrderBy(category => category.OrderId)
                .ToList();

            packet.WriteInteger(categories.Count);
            foreach (var category in categories)
            {
                packet.WriteInteger(category.Id);
                packet.WriteString(category.PublicName ?? string.Empty);
                packet.WriteBoolean(category.RequiredRank <= userRank);
            }

            return packet;
        }

        public static NitroServerPacket ComposeIgnoredUsers(IEnumerable<string> ignoredUsers)
        {
            var names = (ignoredUsers ?? Enumerable.Empty<string>()).ToList();
            var packet = new NitroServerPacket(NitroPacketHeaders.UserIgnored);

            packet.WriteInteger(names.Count);
            foreach (var name in names)
                packet.WriteString(name);

            return packet;
        }

        public static NitroServerPacket ComposeIgnoredUserResult(string username)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.UserIgnoredResult);
            packet.WriteInteger(0);
            packet.WriteString(username ?? string.Empty);
            return packet;
        }

        public static NitroServerPacket ComposeCanCreateRoom(bool hasReachedLimit, int roomLimit)
        {
            var packet = new NitroServerPacket(NitroPacketHeaders.CanCreateRoom);
            packet.WriteInteger(hasReachedLimit ? 1 : 0);
            packet.WriteInteger(roomLimit);
            return packet;
        }

        public static NitroServerPacket ComposeOpenRoomCreator()
        {
            return new NitroServerPacket(NitroPacketHeaders.NavigatorOpenRoomCreator);
        }
    }
}
