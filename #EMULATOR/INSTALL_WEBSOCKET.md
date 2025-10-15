# WebSocket Library Installation Guide

## Installing the NuGet Package

Open **Visual Studio** with your emulator project, then:

### **Option 1: Package Manager Console**
1. Navigate to `Tools` → `NuGet Package Manager` → `Package Manager Console`
2. Execute:
```powershell
Install-Package Fleck -Version 1.2.0
```

### **Option 2: NuGet Package Manager (GUI)**
1. Right-click on project → `Manage NuGet Packages`
2. Search for `Fleck`
3. Install version `1.2.0`

### **Option 3: packages.config**
Add to `packages.config`:
```xml
<package id="Fleck" version="1.2.0" targetFramework="net48" />
```
Then: Right-click on Solution → `Restore NuGet Packages`

## After Installation

1. **Rebuild the project:**
   - `Build` → `Rebuild Solution`

2. **Verify installation:**
   - Check that `Fleck.dll` is in your `bin` folder
   - WebSocket files should compile without errors

## Configuration

### Port Configuration

The emulator runs two servers:
- **Flash Client:** TCP on port `30000` (default)
- **Nitro Client:** WebSocket on port `2096` (default)

To change ports, edit `PlusEnvironment.cs`:
```csharp
// Flash TCP Port
private const int GAME_PORT = 30000;

// Nitro WebSocket Port  
private const int WEBSOCKET_PORT = 2096;
```

### Firewall Rules

Make sure both ports are open:
```powershell
# Windows Firewall
New-NetFirewallRule -DisplayName "Habbo Flash" -Direction Inbound -LocalPort 30000 -Protocol TCP -Action Allow
New-NetFirewallRule -DisplayName "Habbo Nitro" -Direction Inbound -LocalPort 2096 -Protocol TCP -Action Allow
```

## Testing

### Flash Client Test
1. Start emulator
2. Connect Flash client to `localhost:30000`
3. Should work exactly as before

### Nitro Client Test
1. Start emulator
2. Open Nitro client
3. Configure WebSocket URL: `ws://localhost:2096`
4. Login should work

## Troubleshooting

### "Fleck not found" Error
**Solution:** Restore NuGet packages
```powershell
Update-Package -reinstall Fleck
```

### Port Already in Use
**Solution:** Change port in configuration or close conflicting application

### WebSocket Connection Failed
**Solution:** 
- Check firewall settings
- Verify emulator is running
- Check console for error messages

## Additional Information

### Dependencies
- **Fleck 1.2.0** - WebSocket server library
- **.NET Framework 4.8** - Required runtime

### Compatibility
- ✅ Windows 10/11
- ✅ Windows Server 2016+
- ✅ Visual Studio 2019+
- ✅ Flash Client (unchanged)
- ✅ Nitro Client (new support)

---

**Last Updated:** 2025-10-15  
**Version:** 1.0
