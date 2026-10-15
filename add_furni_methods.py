with open(r'C:\xampp\htdocs\#EMULATOR\Communication\WebSocket\WebSocketConnectionHandler.cs', encoding='utf-8') as f:
    c = f.read()

# Find a good place to add the methods - after HandleUserPostureAction
anchor = '''        private void HandleUserPostureAction(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4) return;
                int actionId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int pt = -1;
                if (actionId == 1) pt = 1;
                else if (actionId == 2) pt = 0;
                else if (actionId == 3) pt = 2;
                else return;
                byte[] pd = new byte[4];
                pd[0] = (byte)(pt >> 24); pd[1] = (byte)(pt >> 16); pd[2] = (byte)(pt >> 8); pd[3] = (byte)pt;
                HandleUserPosture(client, pd);
            }
            catch (Exception ex) { log.Error($"Error handling user action: {ex.Message}"); }
        }

        // ===== MESSENGER ====='''

new_methods = '''        private void HandleUserPostureAction(NitroClient client, byte[] data)
        {
            try
            {
                if (data == null || data.Length < 4) return;
                int actionId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int pt = -1;
                if (actionId == 1) pt = 1;
                else if (actionId == 2) pt = 0;
                else if (actionId == 3) pt = 2;
                else return;
                byte[] pd = new byte[4];
                pd[0] = (byte)(pt >> 24); pd[1] = (byte)(pt >> 16); pd[2] = (byte)(pt >> 8); pd[3] = (byte)pt;
                HandleUserPosture(client, pd);
            }
            catch (Exception ex) { log.Error($"Error handling user action: {ex.Message}"); }
        }

        private void HandleFurnitureInteraction(NitroClient client, byte[] data)
        {
            try
            {
                if (!client.IsAuthenticated || client.GetHabbo() == null) return;
                
                var habbo = client.GetHabbo();
                
                bool isInRoom = habbo.InRoom || _currentRooms.ContainsKey(client.ConnectionId);
                
                if (!isInRoom)
                {
                    log.Warn($"Client {client.ConnectionId} tried to interact with furniture but is not in a room");
                    return;
                }

                Room room = null;
                int roomId = habbo.CurrentRoomId > 0 ? habbo.CurrentRoomId : (_currentRooms.TryGetValue(client.ConnectionId, out int trackedRoomId) ? trackedRoomId : 0);
                
                if (roomId <= 0)
                {
                    log.Warn($"Client {client.ConnectionId} tried to interact with furniture but no room ID found");
                    return;
                }
                
                if (!PlusEnvironment.GetGame().GetRoomManager().TryGetRoom(roomId, out room))
                {
                    room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(roomId);
                    if (room == null)
                    {
                        log.Warn($"Room {roomId} not found for client {client.ConnectionId}");
                        return;
                    }
                }

                // Parse packet data - Check if it's binary or string format
                int itemId;
                
                // Try to parse as string first (Flash format: "itemId")
                string rawData = System.Text.Encoding.UTF8.GetString(data);
                if (int.TryParse(rawData.Trim(), out itemId))
                {
                    log.Info($"Furniture interaction (string format): ItemID={itemId}");
                }
                else if (data.Length >= 4)
                {
                    // Binary format: [itemId:4]
                    itemId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                    log.Info($"Furniture interaction (binary format): ItemID={itemId}");
                }
                else
                {
                    log.Warn($"Invalid furniture interaction packet from client {client.ConnectionId}: {rawData}");
                    return;
                }

                var roomUser = room.GetRoomUserManager()?.GetRoomUserByHabbo(habbo.Id);
                if (roomUser == null)
                {
                    log.Warn($"RoomUser not found for habbo {habbo.Id}");
                    return;
                }

                var item = room.GetRoomItemHandler().GetItem(itemId);
                if (item == null)
                {
                    log.Warn($"Item {itemId} not found in room {room.RoomId}");
                    return;
                }

                var baseItem = item.GetBaseItem();
                if (baseItem == null) return;

                log.Info($"[FURNITURE INTERACTION] Processing item {itemId} - Type: {baseItem.InteractionType}, IsSeat: {baseItem.IsSeat}");

                // SEATING FURNITURE (Chairs, Beds, Sofas, etc.)
                if (baseItem.IsSeat || baseItem.InteractionType == InteractionType.BED)
                {
                    if (roomUser.HasStatus("sit") || roomUser.HasStatus("lay"))
                    {
                        // Already sitting/laying - stand up
                        roomUser.RemoveStatus("sit");
                        roomUser.RemoveStatus("lay");
                        roomUser.isSitting = false;
                        roomUser.isLying = false;
                        roomUser.Z += 0.35;
                        log.Info($"[FURNITURE] User stood up from {(baseItem.InteractionType == InteractionType.BED ? "bed" : "chair")}");
                        
                        BroadcastPostureUpdate(room, roomUser, "");
                    }
                    else
                    {
                        // Not sitting/laying - sit or lay down
                        if (baseItem.InteractionType == InteractionType.BED)
                        {
                            // Lay down on bed
                            roomUser.Statusses.Add("lay", "1.0 null");
                            roomUser.isLying = true;
                            roomUser.Z -= 0.35;
                            roomUser.UpdateNeeded = true;
                            
                            BroadcastPostureUpdate(room, roomUser, "lay 1.0 null");
                            log.Info($"[FURNITURE] User laid down on bed at rotation {item.Rotation}");
                        }
                        else
                        {
                            // Sit on chair/sofa
                            roomUser.Statusses.Add("sit", item.Rotation.ToString());
                            roomUser.isSitting = true;
                            roomUser.Z -= 0.35;
                            roomUser.UpdateNeeded = true;
                            
                            BroadcastPostureUpdate(room, roomUser, "sit " + item.Rotation);
                            log.Info($"[FURNITURE] User sat on chair at rotation {item.Rotation}");
                        }
                    }

                    // Broadcast item state update
                    BroadcastToNitroClientsInRoom(room, NitroFloorItemUpdateComposer.Compose(item));
                }
                else
                {
                    log.Info($"[FURNITURE] Item {itemId} interaction type {baseItem.InteractionType} not handled for sitting/laying");
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling furniture interaction: {ex.Message}\n{ex.StackTrace}");
            }
        }

        private void HandleFurnitureFloorUpdate(NitroClient client, byte[] data)
        {
            try
            {
                if (!client.IsAuthenticated || client.GetHabbo() == null) return;
                
                var habbo = client.GetHabbo();
                
                bool isInRoom = habbo.InRoom || _currentRooms.ContainsKey(client.ConnectionId);
                
                if (!isInRoom)
                {
                    log.Warn($"Client {client.ConnectionId} tried to move furniture but is not in a room");
                    return;
                }

                Room room = null;
                int roomId = habbo.CurrentRoomId > 0 ? habbo.CurrentRoomId : (_currentRooms.TryGetValue(client.ConnectionId, out int trackedRoomId) ? trackedRoomId : 0);
                
                if (roomId <= 0) return;
                
                if (!PlusEnvironment.GetGame().GetRoomManager().TryGetRoom(roomId, out room))
                {
                    room = PlusEnvironment.GetGame().GetRoomManager().LoadRoom(roomId);
                    if (room == null) return;
                }

                // Parse packet data - binary format: [itemId:4][x:4][y:4][rotation:4]
                if (data.Length < 16)
                {
                    log.Warn($"Invalid furniture floor update packet from client {client.ConnectionId}");
                    return;
                }

                int itemId = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];
                int x = (data[4] << 24) | (data[5] << 16) | (data[6] << 8) | data[7];
                int y = (data[8] << 24) | (data[9] << 16) | (data[10] << 8) | data[11];
                int rotation = (data[12] << 24) | (data[13] << 16) | (data[14] << 8) | data[15];

                log.Info($"Client {client.ConnectionId} moving furniture: ItemID={itemId}, Pos=({x},{y}), Rot={rotation}");

                var item = room.GetRoomItemHandler().GetItem(itemId);
                if (item == null)
                {
                    log.Warn($"Item {itemId} not found in room");
                    return;
                }

                // Check rights
                if (item.UserID != habbo.Id && !room.CheckRights(habbo.GetClient(), true))
                {
                    log.Warn($"Client {client.ConnectionId} has no rights to move item {itemId}");
                    return;
                }

                // Move the item
                if (room.GetRoomItemHandler().SetFloorItem(habbo.GetClient(), item, x, y, rotation, true, false, true))
                {
                    log.Info($"Successfully moved item {itemId} to ({x},{y}) rotation {rotation}");
                    
                    // Broadcast update
                    BroadcastToNitroClientsInRoom(room, NitroFloorItemUpdateComposer.Compose(item));
                }
            }
            catch (Exception ex)
            {
                log.Error($"Error handling furniture floor update: {ex.Message}\n{ex.StackTrace}");
            }
        }

        // ===== MESSENGER ====='''

if anchor in c:
    c = c.replace(anchor, new_methods, 1)
    with open(r'C:\xampp\htdocs\#EMULATOR\Communication\WebSocket\WebSocketConnectionHandler.cs', 'w', encoding='utf-8') as f:
        f.write(c)
    print("Furniture methods added!")
else:
    print("Anchor not found!")
    idx = c.find('HandleUserPostureAction')
    if idx >= 0:
        print("Found at:", idx)
        print(repr(c[idx:idx+200]))