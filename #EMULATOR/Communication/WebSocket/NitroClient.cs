using System;
using log4net;
using Plus.HabboHotel.Users;
using Plus.HabboHotel.Users.UserData;
using Plus.Communication.Packets.Outgoing.Handshake;
using Plus.Communication.Packets.Outgoing.Users;
using Plus.Communication.Packets.Outgoing.Navigator;
using Plus.Communication.Packets.Outgoing.Inventory;
using Plus.Communication.Packets.Outgoing.Messenger;
using Plus.Database.Interfaces;

namespace Plus.Communication.WebSocket
{
    /// <summary>
    /// Represents a Nitro client connection
    /// </summary>
    public class NitroClient
    {
        private static readonly ILog log = LogManager.GetLogger("Plus.Communication.WebSocket");
        
        private readonly WebSocketConnectionWrapper _connection;
        private Habbo _user;
        private bool _authenticated;
        private string _ssoTicket;

        public int ConnectionId => _connection.ConnectionId;
        public string IP => _connection.IP;
        public Habbo GetHabbo() => _user;
        public bool IsAuthenticated => _authenticated;

        public NitroClient(WebSocketConnectionWrapper connection)
        {
            _connection = connection;
            _authenticated = false;
        }

        /// <summary>
        /// Send initial handshake packets
        /// </summary>
        public void SendHandshake()
        {
            try
            {
                // 1. Send InitCrypto
                SendPacket(NitroInitCryptoComposer.Compose());
                
                log.Info($"Sent InitCrypto to Nitro client {ConnectionId}");
            }
            catch (Exception ex)
            {
                log.Error($"Error sending handshake: {ex.Message}");
            }
        }

        /// <summary>
        /// Authenticate user with SSO ticket
        /// </summary>
        public bool TryAuthenticate(string ssoTicket)
        {
            try
            {
                _ssoTicket = ssoTicket;

                log.Info($"Attempting to authenticate with SSO: '{ssoTicket}'");

                // TEMPORARY: For testing, accept any SSO and use first user
                // TODO: Implement proper SSO token system for Nitro
                if (ssoTicket.StartsWith("NITRO-"))
                {
                    log.Warn("Received default Nitro SSO token - using test user");
                    
                    // Get first user for testing
                    using (IQueryAdapter dbClient = PlusEnvironment.GetDatabaseManager().GetQueryReactor())
                    {
                        dbClient.SetQuery("SELECT `id` FROM `users` ORDER BY `id` ASC LIMIT 1");
                        int userId = dbClient.GetInteger();
                        
                        if (userId > 0)
                        {
                            // Load user with full UserData (including badges)
                            Plus.HabboHotel.Users.UserData.UserData userData = UserDataFactory.GetUserData(userId);
                            if (userData != null && userData.user != null)
                            {
                                _user = userData.user;
                                
                                log.Info($"UserData loaded: {userData.badges.Count} badges in UserData");
                                
                                _user.InitInformation(userData);
                                
                                log.Info($"After InitInformation: BadgeComponent has {_user.GetBadgeComponent()?.Count ?? 0} badges");
                                
                                _authenticated = true;
                                
                                // Send authentication success
                                SendPacket(NitroAuthenticationOKComposer.Compose());
                                
                                // Send user object
                                SendPacket(NitroUserObjectComposer.Compose(_user));
                                
                                // Send user rights & perks
                                SendPacket(NitroUserRightsComposer.Compose(_user));
                                SendPacket(NitroUserPerksComposer.Compose(_user));
                                SendPacket(NitroAvailabilityStatusComposer.Compose());
                                
                                // Send Habbo Club subscription (lifetime club for testing)
                                SendPacket(NitroUserSubscriptionComposer.ComposeLifetimeClub(false));
                                
                                // Send user permissions (club level 1 = basic club)
                                SendPacket(NitroUserPermissionsComposer.Compose(1, 0, false));
                                
                                // Send navigator settings (disabled - causes parsing errors)
                                // SendPacket(NitroNavigatorSettingsComposer.Compose());
                                
                                // Send inventory
                                SendPacket(NitroFurniListComposer.Compose(_user));
                                SendPacket(NitroBadgesComposer.Compose(_user));
                                
                                // Send messenger/friends
                                SendPacket(NitroMessengerInitComposer.Compose(_user));
                                SendPacket(NitroFriendListUpdateComposer.Compose(_user));
                                
                                log.Info($"Nitro client {ConnectionId} authenticated as {_user.Username} (TEST MODE)");
                                
                                return true;
                            }
                        }
                    }
                }

                // Normal SSO authentication
                using (IQueryAdapter dbClient = PlusEnvironment.GetDatabaseManager().GetQueryReactor())
                {
                    dbClient.SetQuery("SELECT `id` FROM `users` WHERE `auth_ticket` = @sso LIMIT 1");
                    dbClient.AddParameter("sso", ssoTicket);
                    int userId = dbClient.GetInteger();

                    if (userId > 0)
                    {
                        // Load user with full UserData (including badges)
                        Plus.HabboHotel.Users.UserData.UserData userData = UserDataFactory.GetUserData(userId);
                        if (userData != null && userData.user != null)
                        {
                            _user = userData.user;
                            _user.InitInformation(userData);
                            _authenticated = true;
                            
                            // Send authentication success
                            SendPacket(NitroAuthenticationOKComposer.Compose());
                            
                            // Send user object
                            SendPacket(NitroUserObjectComposer.Compose(_user));
                            // Send user rights & perks
                            SendPacket(NitroUserRightsComposer.Compose(_user));
                            SendPacket(NitroUserPerksComposer.Compose(_user));
                            SendPacket(NitroAvailabilityStatusComposer.Compose());
                            
                            // Send Habbo Club subscription (lifetime club for testing)
                            SendPacket(NitroUserSubscriptionComposer.ComposeLifetimeClub(false));
                            
                            // Send user permissions (club level 1 = basic club)
                            SendPacket(NitroUserPermissionsComposer.Compose(1, 0, false));
                            
                            // Send navigator settings (disabled - causes parsing errors)
                            // SendPacket(NitroNavigatorSettingsComposer.Compose());
                            
                            // Send inventory
                            SendPacket(NitroFurniListComposer.Compose(_user));
                            SendPacket(NitroBadgesComposer.Compose(_user));
                            
                            // Send messenger/friends
                            SendPacket(NitroMessengerInitComposer.Compose(_user));
                            SendPacket(NitroFriendListUpdateComposer.Compose(_user));
                            
                            // Clear SSO ticket
                            dbClient.SetQuery("UPDATE `users` SET `auth_ticket` = NULL WHERE `id` = @id LIMIT 1");
                            dbClient.AddParameter("id", userId);
                            dbClient.RunQuery();
                            
                            log.Info($"Nitro client {ConnectionId} authenticated as {_user.Username}");
                            
                            return true;
                        }
                    }
                }
                
                log.Warn($"Invalid SSO ticket for Nitro client {ConnectionId}: '{ssoTicket}'");
                return false;
            }
            catch (Exception ex)
            {
                log.Error($"Error authenticating Nitro client: {ex.Message}");
                return false;
            }
        }

        /// <summary>
        /// Send packet to client
        /// </summary>
        public void SendPacket(NitroServerPacket packet)
        {
            try
            {
                byte[] data = packet.GetBytes();
                _connection.SendData(data);
                
                log.Debug($"Sent packet {packet.Header} to Nitro client {ConnectionId}");
            }
            catch (Exception ex)
            {
                log.Error($"Error sending packet: {ex.Message}");
            }
        }

        /// <summary>
        /// Disconnect client
        /// </summary>
        public void Disconnect()
        {
            _connection.Disconnect();
        }

        /// <summary>
        /// Dispose client
        /// </summary>
        public void Dispose()
        {
            _connection.Dispose();
        }
    }
}
