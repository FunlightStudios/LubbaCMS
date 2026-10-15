# 🔍 Nitro Client Packet Analysis
**Reverse Engineering Documentation**

## 📤 Outgoing Headers (Client → Server)

### **Authentication & Security**
- `SECURITY_TICKET = 2419` - SSO Ticket authentication
- `SECURITY_MACHINE = 2490` - Machine ID
- `RELEASE_VERSION = 4000` - Client version
- `CLIENT_POLICY = 26979` - Policy file request
- `CLIENT_PONG = 2596` - Heartbeat response
- `CLIENT_LATENCY = 295` - Latency measurement
- `CLIENT_LATENCY_MEASURE = 96` - Latency measure request

### **User & Profile**
- `USER_INFO = 357` - Request user info
- `USER_CURRENCY = 273` - Request currency info
- `USER_FIGURE = 2730` - Update user figure/look
- `USER_HOME_ROOM = 1740` - Set home room
- `USER_BADGES = 2769` - Request badges
- `USER_BADGES_CURRENT = 2091` - Request current badges
- `USER_BADGES_CURRENT_UPDATE = 644` - Update current badges
- `USER_BOTS = 3848` - Request user bots
- `USER_FURNITURE = 3150` - Request furniture (in room)
- `USER_FURNITURE2 = 3500` - Request furniture (not in room)
- `USER_EFFECT_ACTIVATE = 2959` - Activate effect
- `USER_EFFECT_ENABLE = 1752` - Enable effect
- `USER_IGNORE = 1117` - Ignore user
- `USER_IGNORE_ID = 3314` - Ignore user by ID
- `USER_UNIGNORE = 2061` - Unignore user
- `USER_PETS = 3095` - Request pets
- `USER_PROFILE = 3265` - Request user profile
- `USER_RESPECT = 2694` - Give respect
- `USER_SETTINGS = 1086` - Request user settings
- `USER_SETTINGS_CAMERA = 1461` - Camera settings
- `USER_SETTINGS_CHAT_STYLE = 1030` - Chat style settings
- `USER_SETTINGS_INVITES = 1086` - Invite settings
- `USER_SETTINGS_SAVE = 1968` - Save user settings
- `USER_SETTINGS_VOLUME = 1367` - Volume settings
- `USER_SUBSCRIPTION = 3166` - Request subscription info

### **Room Management**
- `ROOM_CREATE = 2752` - Create room
- `ROOM_DELETE = 532` - Delete room
- `ROOM_ENTER = 2312` - Enter room
- `ROOM_INFO = 2230` - Request room info
- `ROOM_LIKE = 3582` - Like room
- `ROOM_FAVORITE = 3817` - Add room to favorites
- `ROOM_FAVORITE_REMOVE = 309` - Remove room from favorites
- `ROOM_SETTINGS = 3129` - Request room settings
- `ROOM_SETTINGS_SAVE = 1969` - Save room settings
- `ROOM_DOORBELL = 1644` - Ring doorbell
- `ROOM_KICK = 1320` - Kick user from room
- `ROOM_MUTE = 3637` - Mute room
- `ROOM_MUTE_USER = 3485` - Mute user in room
- `ROOM_BAN_GIVE = 1477` - Ban user from room
- `ROOM_BAN_LIST = 2267` - Request ban list
- `ROOM_BAN_REMOVE = 992` - Remove ban
- `ROOM_RIGHTS_GIVE = 808` - Give room rights
- `ROOM_RIGHTS_LIST = 3385` - Request rights list
- `ROOM_RIGHTS_REMOVE = 2064` - Remove rights
- `ROOM_RIGHTS_REMOVE_ALL = 2683` - Remove all rights
- `ROOM_RIGHTS_REMOVE_OWN = 3182` - Remove own rights
- `ROOM_STAFF_PICK = 1918` - Staff pick room
- `ROOM_AMBASSADOR_ALERT = 2996` - Ambassador alert
- `ROOM_MODEL = 2300` - Request room model
- `ROOM_MODEL_BLOCKED_TILES = 1687` - Request blocked tiles
- `ROOM_MODEL_DOOR = 3559` - Request door position
- `ROOM_MODEL_SAVE = 875` - Save room model

### **Navigator**
- `NAVIGATOR_INIT = 2110` - Initialize navigator
- `NAVIGATOR_SEARCH = 249` - Search rooms
- `NAVIGATOR_SEARCH_OPEN = 637` - Open search
- `NAVIGATOR_SEARCH_CLOSE = 1834` - Close search
- `NAVIGATOR_SEARCH_SAVE = 2226` - Save search
- `NAVIGATOR_SETTINGS = 1782` - Request navigator settings
- `NAVIGATOR_SETTINGS_SAVE = 3159` - Save navigator settings
- `NAVIGATOR_CATEGORIES = 3027` - Request categories
- `NAVIGATOR_CATEGORY_LIST_MODE = 1202` - Category list mode

### **Catalog**
- `CATALOG_MODE = 1195` - Request catalog mode
- `CATALOG_PAGE = 412` - Request catalog page
- `CATALOG_PURCHASE = 3492` - Purchase item
- `CATALOG_PURCHASE_GIFT = 1411` - Purchase gift
- `CATALOG_SEARCH = 2594` - Search catalog
- `CATALOG_CLUB = 3285` - Request club page
- `CATALOG_CLUB_GIFTS = 487` - Request club gifts

### **Furniture**
- `FURNITURE_PLACE = 1258` - Place furniture
- `FURNITURE_PICKUP = 3456` - Pickup furniture
- `FURNITURE_FLOOR_UPDATE = 248` - Update floor furniture
- `FURNITURE_WALL_UPDATE = 168` - Update wall furniture
- `FURNITURE_MULTISTATE = 99` - Toggle furniture state
- `FURNITURE_WALL_MULTISTATE = 210` - Toggle wall furniture state
- `FURNITURE_RANDOMSTATE = 3617` - Random furniture state
- `FURNITURE_POSTIT_PLACE = 2248` - Place post-it
- `FURNITURE_ALIASES = 3898` - Request furniture aliases

### **Items**
- `ITEM_DICE_CLICK = 1990` - Click dice
- `ITEM_DICE_CLOSE = 1533` - Close dice
- `ITEM_COLOR_WHEEL_CLICK = 2144` - Click color wheel
- `ITEM_DIMMER_SAVE = 1648` - Save dimmer settings
- `ITEM_DIMMER_SETTINGS = 2813` - Request dimmer settings
- `ITEM_DIMMER_TOGGLE = 2296` - Toggle dimmer
- `ITEM_EXCHANGE_REDEEM = 3115` - Redeem exchange item
- `ITEM_CLOTHING_REDEEM = 3374` - Redeem clothing
- `ITEM_PAINT = 711` - Paint item
- `ITEM_SAVE_BACKGROUND = 3608` - Save background
- `ITEM_STACK_HELPER = 3839` - Stack helper
- `ITEM_WALL_CLICK = 210` - Click wall item
- `ITEM_WALL_UPDATE = 168` - Update wall item

### **Unit Actions (Avatar)**
- `UNIT_WALK = 3320` - Walk
- `UNIT_CHAT = 1314` - Chat
- `UNIT_CHAT_SHOUT = 2085` - Shout
- `UNIT_CHAT_WHISPER = 1543` - Whisper
- `UNIT_ACTION = 2456` - Perform action
- `UNIT_DANCE = 2080` - Dance
- `UNIT_POSTURE = 2235` - Change posture (sit/lay)
- `UNIT_LOOK = 3301` - Look at position
- `UNIT_SIGN = 1975` - Show sign
- `UNIT_TYPING = 1597` - Start typing
- `UNIT_TYPING_STOP = 1474` - Stop typing
- `UNIT_DROP_HAND_ITEM = 2814` - Drop hand item
- `UNIT_GIVE_HANDITEM = 2941` - Give hand item

### **Messenger (Friends)**
- `MESSENGER_INIT = 2781` - Initialize messenger
- `MESSENGER_FRIENDS = 1523` - Request friends list
- `MESSENGER_REQUEST = 3157` - Send friend request
- `MESSENGER_REQUESTS = 2448` - Request friend requests
- `MESSENGER_ACCEPT = 137` - Accept friend request
- `MESSENGER_DECLINE = 2890` - Decline friend request
- `MESSENGER_REMOVE = 1689` - Remove friend
- `MESSENGER_SEARCH = 1210` - Search users
- `MESSENGER_CHAT = 3567` - Send messenger chat
- `MESSENGER_FOLLOW = 3997` - Follow friend
- `MESSENGER_ROOM_INVITE = 1276` - Invite to room
- `MESSENGER_UPDATES = 1419` - Request updates
- `MESSENGER_RELATIONSHIPS = 2138` - Request relationships
- `MESSENGER_RELATIONSHIPS_UPDATE = 3768` - Update relationships

### **Trading**
- `TRADE = 1481` - Start trade
- `TRADE_ACCEPT = 3863` - Accept trade
- `TRADE_CANCEL = 2341` - Cancel trade
- `TRADE_CLOSE = 2551` - Close trade
- `TRADE_CONFIRM = 2760` - Confirm trade
- `TRADE_ITEM = 3107` - Add item to trade
- `TRADE_ITEM_REMOVE = 3845` - Remove item from trade
- `TRADE_ITEMS = 1263` - Request tradeable items
- `TRADE_UNACCEPT = 1444` - Unaccept trade

### **Pets**
- `PET_PLACE = 2647` - Place pet
- `PET_PICKUP = 1581` - Pickup pet
- `PET_INFO = 2934` - Request pet info
- `PET_RESPECT = 3202` - Give pet respect
- `PET_RIDE = 1036` - Ride pet
- `PET_MOVE = 3449` - Move pet

### **Bots**
- `BOT_PLACE = 1592` - Place bot
- `BOT_PICKUP = 3323` - Pickup bot
- `BOT_CONFIGURATION = 1986` - Configure bot
- `BOT_SKILL_SAVE = 2624` - Save bot skill

### **Groups**
- `GROUP_INFO = 2991` - Request group info
- `GROUP_CREATE_OPTIONS = 798` - Request group creation options
- `GROUP_BUY = 230` - Buy/create group
- `GROUP_DELETE = 1134` - Delete group
- `GROUP_SETTINGS = 1004` - Request group settings
- `GROUP_SAVE_INFORMATION = 3137` - Save group information
- `GROUP_SAVE_BADGE = 1991` - Save group badge
- `GROUP_SAVE_COLORS = 1764` - Save group colors
- `GROUP_SAVE_PREFERENCES = 3435` - Save group preferences
- `GROUP_PARTS = 813` - Request group badge parts
- `GROUP_MEMBERS = 312` - Request group members
- `GROUP_MEMBERSHIPS = 367` - Request user's group memberships
- `GROUP_REQUEST = 998` - Request to join group
- `GROUP_REQUEST_ACCEPT = 3386` - Accept group request
- `GROUP_REQUEST_DECLINE = 1894` - Decline group request
- `GROUP_MEMBER_REMOVE = 593` - Remove group member
- `GROUP_MEMBER_REMOVE_CONFIRM = 3593` - Confirm member removal
- `GROUP_ADMIN_ADD = 2894` - Add group admin
- `GROUP_ADMIN_REMOVE = 722` - Remove group admin
- `GROUP_FORUM_INFO = 3149` - Request forum info
- `GROUP_FORUM_LIST = 873` - Request forum list
- `GROUP_FORUM_THREADS = 436` - Request forum threads

### **Moderation Tools**
- `MOD_TOOL_USER_INFO = 3295` - Request user info (mod)
- `MODTOOL_REQUEST_ROOM_INFO = 707` - Request room info (mod)
- `MODTOOL_REQUEST_USER_CHATLOG = 1391` - Request user chatlog
- `MODTOOL_REQUEST_ROOM_CHATLOG = 2587` - Request room chatlog
- `MODTOOL_REQUEST_USER_ROOMS = 3526` - Request user room visits
- `MODTOOL_CHANGE_ROOM_SETTINGS = 3260` - Change room settings (mod)
- `MODTOOL_SANCTION_ALERT = 229` - Send alert sanction
- `MODTOOL_SANCTION_BAN = 2766` - Ban user
- `MODTOOL_SANCTION_KICK = 2582` - Kick user
- `MODTOOL_SANCTION_MUTE = 1945` - Mute user
- `MODTOOL_SANCTION_TRADELOCK = 3742` - Trade lock user
- `MODTOOL_ALERTEVENT = 1840` - Send alert message
- `MODTOOL_ROOM_ALERT = 3842` - Send room alert
- `CONVERT_GLOBAL_ROOM_ID = 314` - Convert global room ID

### **Camera**
- `CAMERA_PRICE = 796` - Request camera price
- `CAMERA_SAVE = 3226` - Save photo
- `CAMERA_THUMBNAIL = 1982` - Request thumbnail

### **Games**
- `GAMES_INIT = 2914` - Initialize games
- `GAMES_LIST = 741` - Request games list

### **Misc**
- `ACHIEVEMENT_LIST = 219` - Request achievements
- `DESKTOP_VIEW = 105` - Desktop view
- `DESKTOP_NEWS = 1827` - Request news
- `DESKTOP_CAMPAIGNS = 2912` - Request campaigns
- `CLIENT_VARIABLES = 1053` - Request client variables
- `CLIENT_TOOLBAR_TOGGLE = 2313` - Toggle toolbar
- `DISCOUNT_CONFIG = 223` - Request discount config
- `EVENT_TRACKER = 3457` - Track event
- `FIND_FRIENDS = 516` - Find friends
- `GIFT_CONFIG = 418` - Request gift config
- `INVENTORY_UNKNOWN = 1371` - Unknown inventory packet
- `MARKETPLACE_CONFIG = 2597` - Request marketplace config
- `RECYCLER_PRIZES = 398` - Request recycler prizes
- `REPORT = 1691` - Report user/room

---

## 📥 Incoming Headers (Server → Client)

### **Currently Implemented in Emulator**
✅ `SECURITY_TICKET = 2419` - Authentication
✅ `USER_INFO = 357` - User info request
✅ `USER_CURRENCY = 273` - Currency request
✅ `ROOM_ENTER = 2312` - Room entry
✅ `UNIT_WALK = 3320` - Walking
✅ `UNIT_CHAT = 1314` - Chat
✅ `CATALOG_PAGE = 412` - Catalog page
✅ `CATALOG_PURCHASE = 3492` - Purchase
✅ `NAVIGATOR_SEARCH = 249` - Navigator search
✅ `BOT_PLACE = 1592` - Bot placement
✅ `BOT_PICKUP = 3323` - Bot pickup
✅ `BOT_CONFIGURATION = 1986` - Bot config
✅ `BOT_SKILL_SAVE = 2624` - Bot skill save
✅ `MODTOOL_REQUEST_ROOM_INFO = 707` - Mod room info
✅ `MODTOOL_REQUEST_USER_CHATLOG = 1391` - Mod user chatlog
✅ `MODTOOL_REQUEST_ROOM_CHATLOG = 2587` - Mod room chatlog
✅ `MODTOOL_REQUEST_USER_ROOMS = 3526` - Mod user rooms
✅ `MODTOOL_ALERTEVENT = 1840` - Mod alert message
✅ `MODTOOL_SANCTION_ALERT = 229` - Mod alert sanction
✅ `MODTOOL_SANCTION_KICK = 2582` - Mod kick
✅ `MODTOOL_SANCTION_BAN = 2766` - Mod ban
✅ `MODTOOL_SANCTION_MUTE = 1945` - Mod mute

### **Priority Implementation Needed**
🔴 **High Priority:**
- `FURNITURE_PLACE = 1258` - Place furniture
- `FURNITURE_PICKUP = 3456` - Pickup furniture
- `FURNITURE_FLOOR_UPDATE = 248` - Move furniture
- `MESSENGER_INIT = 2781` - Friends list
- `MESSENGER_CHAT = 3567` - Messenger chat
- `TRADE = 1481` - Trading system
- `PET_PLACE = 2647` - Pet placement

🟡 **Medium Priority:**
- `GROUP_INFO = 2991` - Group system
- `USER_BADGES = 2769` - Badge system
- `ROOM_SETTINGS_SAVE = 1969` - Room settings
- `NAVIGATOR_INIT = 2110` - Navigator init

🟢 **Low Priority:**
- `CAMERA_SAVE = 3226` - Camera system
- `GAMES_INIT = 2914` - Games
- `GROUP_FORUM_INFO = 3149` - Forums

---

## 🔧 Implementation Status

### **WebSocketConnectionHandler.cs Cases**
```csharp
case 2419: // SECURITY_TICKET ✅
case 357:  // USER_INFO ✅
case 273:  // USER_CURRENCY ✅
case 2312: // ROOM_ENTER ✅
case 3320: // UNIT_WALK ✅
case 1314: // UNIT_CHAT ✅
case 412:  // CATALOG_PAGE ✅
case 3492: // CATALOG_PURCHASE ✅
case 249:  // NAVIGATOR_SEARCH ✅
case 1592: // BOT_PLACE ✅
case 3323: // BOT_PICKUP ✅
case 1986: // BOT_CONFIGURATION ✅
case 2624: // BOT_SKILL_SAVE ✅
case 707:  // MODTOOL_REQUEST_ROOM_INFO ✅
case 2587: // MODTOOL_REQUEST_ROOM_CHATLOG ✅
case 1391: // MODTOOL_REQUEST_USER_CHATLOG ✅
case 3526: // MODTOOL_REQUEST_USER_ROOMS ✅
case 1840: // MODTOOL_ALERTEVENT ✅
case 229:  // MODTOOL_SANCTION_ALERT ✅
case 2582: // MODTOOL_SANCTION_KICK ✅
case 2766: // MODTOOL_SANCTION_BAN ✅
case 1945: // MODTOOL_SANCTION_MUTE ✅
```

### **Missing Critical Handlers**
```csharp
// TODO: Implement these
case 1258: // FURNITURE_PLACE
case 3456: // FURNITURE_PICKUP
case 248:  // FURNITURE_FLOOR_UPDATE
case 2781: // MESSENGER_INIT
case 3567: // MESSENGER_CHAT
case 1481: // TRADE
case 2647: // PET_PLACE
```

---

## 📊 Packet Structure Analysis

### **Common Patterns**

#### **Simple Request (No Data)**
```
Header only, no body
Example: USER_INFO, USER_CURRENCY
```

#### **ID-Based Request**
```
Int: ID (User/Room/Item)
Example: MOD_TOOL_USER_INFO, ROOM_INFO
```

#### **Position-Based**
```
Int: X
Int: Y
Example: UNIT_WALK
```

#### **String Message**
```
String: Message
Int: Optional parameters
Example: UNIT_CHAT
```

---

**Last Updated:** 2025-10-22  
**Analyzed Client:** Nitro (main.js)  
**Emulator:** Plus Emulator (Nitro WebSocket)
