# 📋 LubbaCMS & Emulator - TODO List

**Last Updated:** 2025-10-15  
**Status:** Active Development

This document tracks all planned features, improvements, and known issues for both the CMS and Emulator.

---

## 🎯 High Priority

### Emulator - Nitro Client

#### **Furniture System** 🔴 Critical
- [ ] Furniture placement in rooms
- [ ] Furniture pickup (`:pickall` command)
- [ ] Furniture rotation
- [ ] Furniture interaction (sit, lay on furniture)
- [ ] Furniture state changes (doors, gates, etc.)
- [ ] Furniture stacking
- [ ] Wall items support

#### **Catalog System** 🔴 Critical
- [ ] Catalog index packet
- [ ] Catalog pages
- [ ] Item purchase system
- [ ] Currency deduction
- [ ] Item delivery to inventory
- [ ] Marketplace support

#### **Avatar Actions** 🟡 Important
- [ ] `:sit` command - Sit down
- [ ] `:stand` command - Stand up
- [ ] `:lay` command - Lay down
- [ ] `:dance` command - Dance animations
- [ ] Sitting on furniture
- [ ] Laying on beds
- [ ] Wave gesture
- [ ] Idle animations

### CMS - Frontend

#### **Nitro Client Integration** 🟡 Important
- [ ] Embed Nitro client in CMS
- [ ] Client configuration page
- [ ] WebSocket URL configuration
- [ ] Client switcher (Flash/Nitro toggle)
- [ ] Client loading screen

#### **User Interface** 🟡 Important
- [ ] Update homepage for Nitro
- [ ] Client page redesign
- [ ] Modern UI/UX improvements
- [ ] Mobile responsiveness
- [ ] Dark mode support

---

## 🚀 Medium Priority

### Emulator - Nitro Client

#### **Trading System** 🟡 Important
- [ ] Trade request packets
- [ ] Trade window
- [ ] Item exchange
- [ ] Trade confirmation
- [ ] Trade logging

#### **Messenger/Friends** 🟡 Important
- [ ] Friend requests
- [ ] Friend list updates
- [ ] Private messages
- [ ] Online status updates
- [ ] Friend search

#### **Advanced Commands** 🟡 Important
- [ ] `:mimic` - Mimic another user
- [ ] `:push` - Push users
- [ ] `:pull` - Pull users
- [ ] `:follow` - Follow users
- [ ] `:enable` - Enable effects
- [ ] `:ejectall` - Eject all users from room
- [ ] Moderator commands (`:alert`, `:kick`, `:ban`)
- [ ] Admin commands (`:shutdown`, `:reload`)

#### **Room Features** 🟡 Important
- [ ] Room rights system
- [ ] Room settings (who can enter, etc.)
- [ ] Room password protection
- [ ] Room bans
- [ ] Room mute
- [ ] Room queue system

### CMS - Backend

#### **Admin Panel** 🟡 Important
- [ ] Nitro client statistics
- [ ] WebSocket connection monitoring
- [ ] Real-time user tracking
- [ ] Server health dashboard
- [ ] Packet logging viewer

#### **User Management** 🟡 Important
- [ ] SSO token generation for Nitro
- [ ] Client preference (Flash/Nitro)
- [ ] User session management
- [ ] Multi-session handling

---

## 🔮 Low Priority / Future Features

### Emulator - Nitro Client

#### **Wired System** 🟢 Future
- [ ] Wired triggers
- [ ] Wired effects
- [ ] Wired conditions
- [ ] Wired stacking
- [ ] Custom wired boxes

#### **Pets System** 🟢 Future
- [ ] Pet placement in rooms
- [ ] Pet movement
- [ ] Pet commands
- [ ] Pet interaction
- [ ] Pet training
- [ ] Pet breeding

#### **Groups System** 🟢 Future
- [ ] Group creation
- [ ] Group management
- [ ] Group rooms
- [ ] Group badges
- [ ] Group forum

#### **Achievements** 🟢 Future
- [ ] Achievement system
- [ ] Achievement unlocks
- [ ] Achievement notifications
- [ ] Achievement display
- [ ] Leaderboards

#### **Bots System** 🟢 Future
- [ ] Bot placement
- [ ] Bot movement
- [ ] Bot speech
- [ ] Bot commands
- [ ] Custom bot scripts

#### **Games** 🟢 Future
- [ ] Freeze (snowball fight)
- [ ] Battle Banzai
- [ ] Football
- [ ] Custom games

### CMS - Features

#### **Hotel Management** 🟢 Future
- [ ] News system
- [ ] Events calendar
- [ ] Staff applications
- [ ] Help/Support tickets
- [ ] Community features

#### **Shop System** 🟢 Future
- [ ] VIP packages
- [ ] Rare items shop
- [ ] Credits purchase
- [ ] Diamonds/Duckets
- [ ] Payment integration

---

## 🐛 Known Issues

### Emulator - Nitro Client

#### **Critical Bugs** 🔴
- None currently! All critical bugs have been fixed ✅

#### **Minor Issues** 🟡
- [ ] Sign duration not configurable (hardcoded 2 seconds)
- [ ] Movement speed not configurable (hardcoded 500ms)
- [ ] No pathfinding optimization (always uses simple A*)
- [ ] No movement queue (clicking multiple times cancels previous movement)

#### **Limitations** 🟢
- [ ] Commands are hardcoded (not using Plus Emulator's command system)
- [ ] No permission system for Nitro commands
- [ ] No multi-user support (only one user can be in one room)
- [ ] No room spectator mode

### CMS - Known Issues

#### **Critical Bugs** 🔴
- None currently!

#### **Minor Issues** 🟡
- [ ] No Nitro client integration yet (available under /nitro)
- [ ] Flash client still default
- [ ] No WebSocket status indicator

---

## ✅ Recently Completed

### Emulator - Nitro Client (2025-10-15)
- ✅ WebSocket server implementation
- ✅ Binary packet protocol
- ✅ Authentication system
- ✅ Room creation & entry
- ✅ Avatar movement with pathfinding
- ✅ Collision detection (furniture + model)
- ✅ Z-height calculation (stairs/levels)
- ✅ Navigator (My Rooms, Popular Rooms)
- ✅ Chat system
- ✅ Basic commands (`:commands`, `:about`)
- ✅ Signs & emotes
- ✅ User look/rotation
- ✅ Room door rendering
- ✅ Room thickness rendering
- ✅ Heightmap generation

### Bug Fixes (2025-10-15)
- ✅ Fixed room creation dialog not closing
- ✅ Fixed avatar teleporting on last movement tile
- ✅ Fixed avatar walking beside tiles (Z-height issue)
- ✅ Fixed sign teleporting avatar (Z-height issue)
- ✅ Fixed walking through walls (collision detection)
- ✅ Fixed missing door in rooms

---

## 🎯 Roadmap

### Phase 1: Core Functionality ✅ **COMPLETE**
- [x] WebSocket Infrastructure
- [x] Authentication
- [x] Room System
- [x] Movement & Pathfinding
- [x] Navigator
- [x] Chat & Basic Commands

### Phase 2: Game Features 🔄 **IN PROGRESS** (40% Complete)
- [x] Commands System
- [ ] Furniture System
- [ ] Catalog
- [ ] Trading
- [ ] Avatar Actions (sit, stand, lay)

### Phase 3: Advanced Features 🔮 **PLANNED** (10% Complete)
- [ ] Wired System
- [ ] Pets
- [ ] Groups
- [ ] Achievements
- [ ] Bots
- [ ] Games

### Phase 4: CMS Integration 🔮 **PLANNED**
- [ ] Nitro Client Embed
- [ ] Admin Dashboard
- [ ] User Management
- [ ] Shop System

---

## 🤝 Contributing

Want to help? Here's how:

### For Developers

1. **Pick a task** from the High Priority section
2. **Fork the repository**
3. **Create a feature branch**: `git checkout -b feature/furniture-system`
4. **Implement the feature** (see implementation notes below)
5. **Test thoroughly** with both Flash and Nitro clients
6. **Submit a pull request**

### Implementation Notes

#### **Furniture System**
- Study existing furniture packets in Flash client
- Implement `NitroRoomFloorItemsComposer` (already exists, needs testing)
- Add furniture placement packet handling
- Add furniture interaction packets
- Test with various furniture types

#### **Catalog System**
- Implement `NitroC catalogIndexComposer`
- Implement `NitroCatalogPageComposer`
- Add purchase packet handling
- Test currency deduction
- Test item delivery

#### **Commands**
- Extend `HandleNitroCommand()` in `WebSocketConnectionHandler.cs`
- Add new command cases
- Implement command logic
- Add permission checks (future)
- Test command execution

### Testing Guidelines

1. **Flash Client Test**: Ensure changes don't break Flash functionality
2. **Nitro Client Test**: Test new features in Nitro client
3. **Dual-Client Test**: Run both clients simultaneously
4. **Database Test**: Verify database changes
5. **Performance Test**: Check for memory leaks or performance issues

### Code Style

- Follow existing C# conventions
- Use meaningful variable names
- Add XML documentation comments
- Keep methods focused and small
- Handle exceptions properly
- Log important events

---

## 📚 Resources

### Documentation
- [Emulator README](#EMULATOR/README.md)
- [Installation Guide](#EMULATOR/INSTALL_WEBSOCKET.md)
- [Implementation Status](#EMULATOR/NITRO_IMPLEMENTATION_STATUS.md)

### Useful Links
- [Nitro Renderer Documentation](https://github.com/billsonnn/nitro-renderer)
- [Habbo Packet Documentation](https://github.com/xabbo/xabbo)
- [Plus Emulator Wiki](https://github.com/billsonnn/PlusEMU)

### Community
- [DevBest Forum](https://devbest.com/)
- [Habbo Development Discord](https://discord.gg/habbo)

---

## 📝 Notes

### Priority Levels
- 🔴 **Critical** - Blocking core functionality
- 🟡 **Important** - Needed for good user experience
- 🟢 **Future** - Nice to have, not urgent

### Status Indicators
- ✅ **Complete** - Fully implemented and tested
- 🔄 **In Progress** - Currently being worked on
- 🔮 **Planned** - Scheduled for future development
- ⏳ **Coming Soon** - Next in queue

---

**Last Updated:** 2025-10-15  
**Maintained By:** LubbaCMS Team  
**Contributors Welcome!** 🎉
