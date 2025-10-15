# LubbaCMS

A modern Content Management System for Habbo Retro hotels, based on BrainCMS. **Now updated for PHP 8+ with Nitro WebSocket support!**

## 🚀 Major Updates!

✅ **PHP 8.0+ Migration Complete**  
✅ **Nitro Client Support (WebSocket)**  
✅ **Flash Client Support (TCP)**  
✅ **Dual-Protocol Emulator**  
✅ **Enhanced Security Features**  
✅ **Type-Safe Code**  
⚠️ **Debug/Development Status** - Core features working, advanced features in progress

## 🎮 Nitro Support Now Available!

The emulator now supports **both Flash and Nitro clients simultaneously**:
- **Flash Client:** Traditional TCP connection (Port 30000)
- **Nitro Client:** Modern WebSocket connection (Port 2096)

### Nitro Features:
- ✅ Full room system with movement
- ✅ Pathfinding with collision detection
- ✅ Multi-level rooms (stairs support)
- ✅ Chat & commands system
- ✅ Navigator with room creation
- ✅ Signs & emotes
- ⏳ Furniture system (coming soon)
- ⏳ Catalog (coming soon)

## Screenshots
!SOON

## Features

### Core Functionality
- Dynamic homepage with hotel statistics
- Secure user authentication system
- Admin panel for managing users, servers, and content
- Customizable front-end
- Fully functional shop and in-game currency system

### Hotel Features
- User registration and login
- Player inventory management
- Virtual currency integration
- Chatroom functionality via the hotel client
- Interactive staff panel with user moderation tools

### Admin Features
- Manage users, bans, and roles
- Edit and update hotel content directly
- Control virtual currency distribution
- Customize hotel settings and themes

### Client Support
- ✅ **Adobe Flash Client** - Fully supported (Port 30000)
- ✅ **Nitro Client** - Fully supported (WebSocket Port 2096)
- ✅ **Dual-Protocol** - Both clients can run simultaneously
- ✅ **No Flash Required** - Use Nitro for a modern, Flash-free experience

## Technology Stack

### Backend
- **PHP 8.0+** (Required) with strict types
- MySQL 5.7+ / MariaDB 10.3+ for database management
- PDO with prepared statements for security
- Bcrypt password hashing (cost 12)
- CSRF protection and rate limiting
- Secure session handling with httpOnly cookies
- Integrated with emulator for hotel functionality

### Frontend
- HTML5, CSS3, and JavaScript
- Responsive and customizable UI
- jQuery for dynamic features

### Emulator
- **Plus Emulator (LubbaEMU)** with Nitro WebSocket support
- Located in `#EMULATOR` folder
- **Dual-Protocol Support:**
  - Flash Client: TCP on port 30000
  - Nitro Client: WebSocket on port 2096
- **Fleck WebSocket Library** (v1.2.0) for Nitro support
- Compatible with the LubbaCMS database

**📚 Documentation:**
- [Emulator README](#EMULATOR/README.md) - Complete overview and setup guide
- [Installation Guide](#EMULATOR/INSTALL_WEBSOCKET.md) - WebSocket library installation
- [Implementation Status](#EMULATOR/NITRO_IMPLEMENTATION_STATUS.md) - Detailed feature status
- [TODO List](TODO.md) - Planned features and contribution guide

## Quick Start

### Emulator Setup (Nitro Support)

For detailed emulator setup instructions, see the [Emulator README](#EMULATOR/README.md).

**Quick Steps:**
1. Install Fleck WebSocket library (see [Installation Guide](#EMULATOR/INSTALL_WEBSOCKET.md))
2. Build the emulator in Visual Studio
3. Run the emulator - both Flash and Nitro clients will work simultaneously

**Ports:**
- Flash Client: `localhost:30000` (TCP)
- Nitro Client: `ws://localhost:2096` (WebSocket)

## Installation

### CMS Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/FunlightStudios/LubbaCMS.git
   cd LubbaCMS
   ```

2. Configure your web server (Apache/Nginx) to point to the project directory

3. Import the database schema

4. Update database configuration in config files

5. Set up the emulator (see [Emulator README](#EMULATOR/README.md))

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### For Developers

1. **Check the [TODO List](TODO.md)** for open tasks
2. **Pick a task** that interests you (High Priority tasks are most needed)
3. **Fork the repository** and create a feature branch
4. **Implement your changes** following our code style
5. **Test thoroughly** with both Flash and Nitro clients
6. **Submit a pull request** with a clear description

### Contribution Guidelines

- **Code Style:** Follow existing C# and PHP conventions
- **Testing:** Test with both Flash and Nitro clients
- **Documentation:** Update relevant documentation
- **Compatibility:** Don't break Flash client functionality
- **Commits:** Use clear, descriptive commit messages

### Areas We Need Help With

🔴 **High Priority:**
- Furniture system for Nitro
- Catalog implementation
- Avatar actions (sit, stand, lay)

🟡 **Important:**
- Trading system
- Advanced commands
- CMS Nitro integration

See the [TODO List](TODO.md) for a complete list of tasks.

## 📜 License

Based on Plus Emulator (PlusEMU) and LubbaEMU.

## 🙏 Credits

- **Sledmore** - Plus Emulator Founder
- **DevBest Community** - Plus Emulator Development
- **Lubba Hotel** - LubbaEMU Base
- **Nitro Team** - Nitro Client Development
- **Contributors** - Everyone who helps improve this project

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-15  
**Status:** Debug/Development (Core Features Working)
