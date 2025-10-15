# 🎮 Plus Emulator - Nitro WebSocket Edition

## Overview

This is a modified version of Plus Emulator (LubbaEMU) with **full Nitro WebSocket support**!

### ✨ Features

- ✅ **Dual-Protocol Support:** Flash (TCP) + Nitro (WebSocket) running simultaneously
- ✅ **Flash Client:** Works exactly as before on port 30000
- ✅ **Nitro Client:** WebSocket support on port 2096
- ✅ **Room System:** Full room creation, navigation, and movement
- ✅ **Avatar Movement:** Smooth pathfinding with collision detection
- ✅ **Z-Height Support:** Proper handling of stairs and multi-level rooms
- ✅ **Commands:** Basic command system (:commands, :about, etc.)
- ✅ **Navigator:** My Rooms & Popular Rooms categories

## 🚀 Quick Start

### Prerequisites

- Visual Studio 2019 or later
- .NET Framework 4.8
- MySQL/MariaDB Database
- Fleck WebSocket Library (NuGet)

### Installation

1. **Install WebSocket Library:**
   ```powershell
   Install-Package Fleck -Version 1.2.0
   ```
   See [INSTALL_WEBSOCKET.md](INSTALL_WEBSOCKET.md) for details

2. **Build the Emulator:**
   - Open solution in Visual Studio
   - Build → Rebuild Solution

3. **Configure Database:**
   - Import database schema
   - Update connection string in config

4. **Run the Emulator:**
   - Start the emulator
   - Flash client: `localhost:30000`
   - Nitro client: WebSocket on `ws://localhost:2096`

## 📊 Implementation Status

See [NITRO_IMPLEMENTATION_STATUS.md](NITRO_IMPLEMENTATION_STATUS.md) for detailed status.

### Working Features:

- ✅ WebSocket Server (Port 2096)
- ✅ Authentication System
- ✅ Room Creation & Entry
- ✅ Avatar Movement & Pathfinding
- ✅ Collision Detection
- ✅ Z-Height Calculation (Stairs/Levels)
- ✅ Navigator (My Rooms, Popular Rooms)
- ✅ Basic Commands
- ✅ Chat System
- ✅ Signs & Emotes

### In Progress:

- ⏳ Furniture System
- ⏳ Catalog
- ⏳ Trading
- ⏳ Advanced Commands

## 🔧 Technical Details

### Architecture

```
Flash Client (TCP:30000) ──┐
                            ├──> Plus Emulator Core
Nitro Client (WS:2096)  ───┘
```

### Key Components

- **WebSocketServer:** Handles WebSocket connections
- **NitroClient:** Manages Nitro client sessions
- **WebSocketConnectionHandler:** Routes packets and handles game logic
- **NitroServerPacket:** Binary packet composer for Nitro protocol

### Packet Format

```
[Length:4 bytes][Header:2 bytes][Data:n bytes]
```

## 📝 Documentation

- [Installation Guide](INSTALL_WEBSOCKET.md)
- [Implementation Status](NITRO_IMPLEMENTATION_STATUS.md)
- [WebSocket Files](ADD_WEBSOCKET_FILES.md)

## 🎯 Roadmap

### Phase 1: Core Functionality ✅
- [x] WebSocket Infrastructure
- [x] Authentication
- [x] Room System
- [x] Movement & Pathfinding
- [x] Navigator

### Phase 2: Game Features (In Progress)
- [x] Commands
- [ ] Furniture Placement
- [ ] Catalog
- [ ] Trading

### Phase 3: Advanced Features
- [ ] Wired System
- [ ] Pets
- [ ] Groups
- [ ] Achievements

## 🤝 Contributing

Contributions are welcome! Please ensure:
- Code follows existing style
- Flash client compatibility is maintained
- Changes are tested with both Flash and Nitro clients

## 📜 License

Based on Plus Emulator (PlusEMU) and LubbaEMU.

## 🙏 Credits

- **Sledmore** - Plus Emulator Founder
- **DevBest Community** - Plus Emulator Development
- **Lubba Hotel** - LubbaEMU Base
- **Nitro Team** - Nitro Client Development

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-15  
**Status:** Debug Ready (Core Features)
