namespace Plus.Communication.Packets.Outgoing.Handshake
{
    /// <summary>
    /// Nitro Client Packet Headers (Outgoing)
    /// These are the packet IDs that Nitro expects
    /// </summary>
    public static class NitroPacketHeaders
    {
        // Handshake & Authentication
        public const int InitCrypto = 277;
        public const int AuthenticationOK = 2491;
        public const int UniqueMachineID = 1488;
        public const int Ping = 3928;
        public const int Pong = 3928;
        
        // User Data
        public const int UserObject = 2725;
        public const int UserRights = 2;
        public const int AvailabilityStatus = 2033;
        public const int UserPerks = 2586;
        
        // Room
        public const int RoomForward = 160;
        public const int RoomEnter = 758;
        public const int RoomModelName = 2031;
        public const int RoomModel = 1301;
        public const int RoomHeightMap = 2753;
        public const int RoomPaint = 2454;
        public const int RoomThickness = 3547;
        public const int RoomModelDoor = 1664;
        public const int Unit = 374;
        public const int UnitStatus = 1640;
        public const int UnitChat = 1446;
        
        // Navigator
        public const int NavigatorSettings = 518;
        public const int NavigatorMetaData = 3052;
        public const int NavigatorLiftedRooms = 3104;
        public const int NavigatorCollapsedCategories = 1834;
        public const int NavigatorSearchResults = 2690;
        
        // Catalog
        public const int CatalogIndex = 1032;
        public const int CatalogPage = 804;
        public const int CatalogOffer = 2347;
        public const int GiftConfiguration = 2234;
        
        // Inventory
        public const int FurniList = 3151;
        public const int BadgeList = 717;
        public const int BotInventory = 3086;
        public const int PetInventory = 3522;
        
        // Messenger
        public const int FriendListUpdate = 2013;
        public const int MessengerInit = 1605;
        public const int NewConsole = 1587;
        public const int FriendNotification = 3082;
        
        // Moderation
        public const int ModeratorInit = 3639;
        public const int IssueInfo = 3192;
        
        // Notifications
        public const int NotificationDialog = 1992;
        public const int BroadcastMessageAlert = 3801;
        public const int MOTDNotification = 2035;
        
        // Achievements
        public const int Achievements = 305;
        public const int AchievementUnlocked = 2596;
        
        // Generic
        public const int GenericAlert = 3801;
        public const int ConnectionError = 1004;
    }
}
