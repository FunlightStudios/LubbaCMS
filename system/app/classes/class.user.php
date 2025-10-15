<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * User Management Class
 * PHP 8+ compatible with strict types, return types, and modern security practices
 * 
 * @package LubbaCMS
 * @version 2.0
 */
class User {
	
	/**
	 * Verify user password with automatic migration from MD5 to Bcrypt
	 * 
	 * @param string $password Plain text password
	 * @param string $passwordDb Hashed password from database
	 * @param string $username Username for migration
	 * @return bool True if password is correct
	 */
	public static function checkUser(string $password, string $passwordDb, string $username): bool {
		global $dbh;
		
		// Modern bcrypt/argon2 hash
		if (str_starts_with($passwordDb, '$')) {
			return password_verify($password, $passwordDb);
		}
		
		// Legacy MD5 - migrate to bcrypt if correct
		if (md5($password) === $passwordDb) {
			try {
				$passwordBcrypt = self::hashed($password);
				$stmt = $dbh->prepare("UPDATE users SET password = :password WHERE username = :username");
				$stmt->bindParam(':username', $username, PDO::PARAM_STR);
				$stmt->bindParam(':password', $passwordBcrypt, PDO::PARAM_STR);
				$stmt->execute();
				return true;
			} catch (PDOException $e) {
				error_log('Password migration failed for user: ' . $username . ' - ' . $e->getMessage());
				return true; // Still allow login even if migration fails
			}
		}
		
		return false;
	}
	
	/**
	 * Hash password using modern algorithm (Bcrypt with cost 12)
	 * 
	 * @param string $password Plain text password
	 * @return string Hashed password
	 */
	public static function hashed(string $password): string {
		return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
	}
	
	/**
	 * Validate username format
	 * 
	 * @param string $username Username to validate
	 * @return bool True if valid
	 */
	public static function validName(string $username): bool {
		$length = strlen($username);
		return $length >= 3 && $length <= 12 && ctype_alnum($username);
	}
	
	/**
	 * Validate email format
	 * 
	 * @param string $email Email to validate
	 * @return bool True if valid
	 */
	public static function validEmail(string $email): bool {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	/**
	 * Validate password strength
	 * 
	 * @param string $password Password to validate
	 * @return bool True if strong enough
	 */
	public static function validPassword(string $password): bool {
		return strlen($password) >= 6;
	}
	
	/**
	 * Get user data for logged-in user
	 * 
	 * @param string $key Database column name
	 * @return mixed User data (filtered for strings, raw for numbers)
	 */
	public static function userData(string $key): mixed {
		global $dbh, $config;
		
		if (!loggedIn()) {
			return null;
		}
		
		// Arcturus emulator special handling
		if ($config['hotelEmu'] === 'arcturus') {
			if (in_array($key, ['activity_points', 'vip_points'], true)) {
				$type = match($key) {
					'activity_points' => '0',
					'vip_points' => '5',
					default => '0'
				};
				
				$stmt = $dbh->prepare("SELECT amount FROM users_currency WHERE user_id = :id AND type = :type");
				$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
				$stmt->bindParam(':type', $type, PDO::PARAM_STR);
				$stmt->execute();
				
				if ($stmt->rowCount() > 0) {
					$row = $stmt->fetch();
					return (int) $row['amount'];
				}
				return 0;
			}
		}
		
		// Standard query for all other fields
		$stmt = $dbh->prepare("SELECT {$key} FROM users WHERE id = :id");
		$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() === 0) {
			return null;
		}
		
		$row = $stmt->fetch();
		$value = $row[$key] ?? null;
		
		// Only filter string values for XSS protection
		// Keep numeric values as-is for proper type handling
		if (is_string($value)) {
			return filter($value);
		}
		
		return $value;
	}
	/**
	 * Check if email is already registered
	 * 
	 * @param string $email Email to check
	 * @return bool True if taken
	 */
	public static function emailTaken(string $email): bool {
		global $dbh;
		$stmt = $dbh->prepare("SELECT mail FROM users WHERE mail = :email LIMIT 1");
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->rowCount() > 0;
	}
	
	/**
	 * Check if username is already taken
	 * 
	 * @param string $username Username to check
	 * @return bool True if taken
	 */
	public static function userTaken(string $username): bool {
		global $dbh;
		$stmt = $dbh->prepare("SELECT username FROM users WHERE username = :username LIMIT 1");
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->rowCount() > 0;
	}
		public static function refUser($refUsername)
		{
			global $dbh, $lang;
			$getUsernameRef = $dbh->prepare("SELECT username,ip_reg FROM users WHERE username = :username LIMIT 1");
			$getUsernameRef->bindParam(':username', $refUsername);
			$getUsernameRef->execute();
			$getUsernameRefData = $getUsernameRef->fetch();
			if ($getUsernameRef->RowCount() > 0)
			{
				if ($getUsernameRefData['ip_reg'] == userIp())
				{
					//html::error($lang["RsameIpRef"]);
					echo 'ref_error';
				}
				else
				{
					return true;
				}
			}
			else
			{	
				//html::error($lang["RnotExist"]);
				echo 'ref_error';
				return false;
			}
		}
		public static function login()
		{
			global $dbh,$config,$lang,$emuUse;
			if (isset($_POST['login']))
			{
				if (!empty($_POST['username']))
				{
					if (!empty($_POST['password']))
					{
						$stmt = $dbh->prepare("SELECT id, password, username, rank FROM users WHERE username = :username");
						$stmt->bindParam(':username', $_POST['username']); 
						$stmt->execute();
						if ($stmt->RowCount() == 1)
						{
							$row = $stmt->fetch();
							if (self::checkUser($_POST['password'], $row['password'],$row['username']))
							{	
								$_SESSION['id'] = $row['id'];
								if (!$config['maintenance'] == true)
								{
									$userUpdateIp = $dbh->prepare("UPDATE users SET ".$emuUse['ip_last']." = :userip WHERE id = :id");
									$userUpdateIp->bindParam(':id', $_SESSION['id']);
									$userUpdateIp->bindParam(':userip', userIp());
									$userUpdateIp->execute(); 
									//User Session Log//
									$insertUserSession = $dbh->prepare("
									INSERT INTO
									user_session_log
									(userid,ip,date,browser)
									VALUES
									(
									:userid, 
									:ip,
									:date,
									:browser
									)");
									$insertUserSession->bindParam(':userid', $_SESSION['id']);
									$insertUserSession->bindParam(':ip', userIp());
									$insertUserSession->bindParam(':date', strtotime('now'));
									$insertUserSession->bindParam(':browser', $_SERVER['HTTP_USER_AGENT']);
									$insertUserSession->execute();
									header('Location: '.$config['hotelUrl'].'/me');
								}
								else
								{	
									if ($row['rank'] >= $config['maintenancekMinimumRankLogin'])
									{
										$_SESSION['adminlogin'] = true;
										header('Location: '.$config['hotelUrl'].'/me');	
									}
									return html::error($lang["Mnologin"]);
								}
							}
							return html::error($lang["Lpasswordwrong"]);
						}
						return html::error($lang["Lnotexistuser"]);
					}
					return html::error($lang["Lnopassword"]);
				}
				return html::error($lang["Lnousername"]);
			}
		}
		public static function register()
		{
			$userRealIp = userIp();
			global $config, $lang, $dbh,$emuUse;
			if (isset($_POST['register']))
			{
				if ($config['registerEnable'] == true)
				{
					if (!empty($_POST['username']))
					{
						if (self::validName($_POST['username']))
						{
							if (!empty($_POST['password']))
							{
								if (!empty($_POST['password_repeat']))
								{
									if (!empty($_POST['email']))
									{
										if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
										{
											if (!self::userTaken($_POST['username']))
											{
												if (!self::emailTaken($_POST['email']))
												{
													if (strlen($_POST['password']) >= 6)
													{
														if ($_POST['password'] == $_POST['password_repeat'])
														{	
															$stmt = $dbh->prepare("SELECT ".$emuUse['ip_last']." FROM users WHERE ".$emuUse['ip_last']." = :userip");
															$stmt->bindParam(':userip',  userIp());
															$stmt->execute();
															if ($stmt->RowCount() < 4)
															{
																if (self::refUser($_POST['referrer']) || empty($_POST['referrer']))
																{
																	if(!$config['recaptchaSiteKeyEnable'] == true)
																	{
																		$_POST['g-recaptcha-response'] = true;
																	}
																	if ($_POST['g-recaptcha-response'])
																	{			
																		$motto = filter($_POST['motto'] );
																		$avatar = filter($_POST['avatar']);
																		$password = self::hashed($_POST['password']);
																		if ($config['hotelEmu'] == 'arcturus')
																		{
																			$addNewUser = $dbh->prepare("
																			INSERT INTO
																			users
																			(username, password, rank, auth_ticket, motto, account_created, last_online, mail, look, ip_current, ip_register, credits)
																			VALUES
																			(
																			:username, 
																			:password, 
																			'1',
																			:sso,
																			:motto, 
																			:time, 
																			:last_online,
																			:email, 
																			:avatar,
																			:userip, 
																			:userip, 
																			:credits
																			)");
																			$addNewUser->bindParam(':username', $_POST['username']);
																			$addNewUser->bindParam(':password', $password);
																			$addNewUser->bindParam(':motto', $motto);
																			$addNewUser->bindParam(':sso', game::sso('register'));
																			$addNewUser->bindParam(':email', $_POST['email']);
																			$addNewUser->bindParam(':avatar', $avatar);
																			$addNewUser->bindParam(':credits', $config['credits']);
																			$addNewUser->bindParam(':userip', userIp());
																			$addNewUser->bindParam(':time', strtotime('now'));
																			$addNewUser->bindParam(':last_online', strtotime('now'));
																			$addNewUser->execute();
																			
																			
																		}
																		else
																		{
																			$addNewUser = $dbh->prepare("
																			INSERT INTO
																			users
																			(username, password, rank, auth_ticket, motto, account_created, last_online, mail, look, ip_last, ip_reg, credits, activity_points, vip_points)
																			VALUES
																			(
																			:username, 
																			:password, 
																			'1',
																			:sso,
																			:motto, 
																			:time, 
																			:last_online,
																			:email, 
																			:avatar,
																			:userip, 
																			:userip, 
																			:credits,
																			:duckets,
																			:diamonds
																			)");
																			$addNewUser->bindParam(':username', $_POST['username']);
																			$addNewUser->bindParam(':password', $password);
																			$addNewUser->bindParam(':motto', $motto);
																			$addNewUser->bindParam(':sso', game::sso('register'));
																			$addNewUser->bindParam(':email', $_POST['email']);
																			$addNewUser->bindParam(':avatar', $avatar);
																			$addNewUser->bindParam(':credits', $config['credits']);
																			$addNewUser->bindParam(':duckets', $config['duckets']);
																			$addNewUser->bindParam(':diamonds', $config['diamonds']);
																			$addNewUser->bindParam(':userip', userIp());
																			$addNewUser->bindParam(':time', strtotime('now'));
																			$addNewUser->bindParam(':last_online', strtotime('now'));
																			$addNewUser->execute();
																		}
																		$lastId = $dbh->lastInsertId();
																		//User referrer//
																		if (!empty($_POST['referrer']))
																		{	
																			$getUserRef = $dbh->prepare("SELECT id,username FROM users WHERE username = :username LIMIT 1");
																			$getUserRef->bindParam(':username', $_POST['referrer']);
																			$getUserRef->execute();
																			$getInfoRefUser = $getUserRef->fetch();
																			$addRef = $dbh->prepare("
																			INSERT INTO
																			referrer
																			(userid, refid,diamonds)
																			VALUES
																			(
																			:lastid, 
																			:refid,
																			:diamonds
																			)");
																			$addRef->bindParam(':lastid', $lastId);
																			$addRef->bindParam(':refid', $getInfoRefUser['id']);
																			$addRef->bindParam(':diamonds', $config['diamondsRef']);
																			$addRef->execute();
																			$stmt = $dbh->prepare("SELECT*FROM referrerbank WHERE userid = :id LIMIT 1");
																			$stmt->bindParam(':id', $getInfoRefUser['id']);
																			$stmt->execute();
																			if ($stmt->RowCount() == 0)
																			{
																				$addDiamondsRow = $dbh->prepare("
																				INSERT INTO
																				referrerbank
																				(userid,diamonds)
																				VALUES
																				(
																				:lastid, 
																				:diamonds
																				)");
																				$addDiamondsRow->bindParam(':lastid', $getInfoRefUser['id']);
																				$addDiamondsRow->bindParam(':diamonds', $config['diamondsRef']);
																				$addDiamondsRow->execute();
																			}
																			else
																			{
																				$addDiamonds = $dbh->prepare("
																				UPDATE referrerbank SET 
																				diamonds=diamonds + :diamonds 
																				WHERE 
																				userid=:lastid
																				");
																				$addDiamonds->bindParam(':lastid', $getInfoRefUser['id']);
																				$addDiamonds->bindParam(':diamonds', $config['diamondsRef']);
																				$addDiamonds->execute(); 
																			}
																			$_SESSION['id'] = $lastId;
																			echo 'succes';
																			return;
																		}
																		//User referrer//
																		else
																		{
																			$_SESSION['id'] = $lastId;
																			echo 'succes';
																			return;
																		}
																	}
																	else
																	{
																		echo 'robot';
																		return;
																	}
																}
															}
															else
															{
																echo 'to_many_ip';
																return;
															}
														}
														else
														{
															echo 'password_repeat_error';
															return;
														}
													}
													else
													{
														echo 'short_password';
														return;
													}
												}
												else
												{
													echo 'used_email';
													return;
												}
											}
											else
											{
												echo 'used_username';
												return;
											}
										}
										else
										{
											echo 'valid_email';
											return;
										}
									}
									else
									{
										echo 'empty_email';
										return;
									}
								}
								else
								{
									echo 'empty_password_repeat';
									return;
								}
							}
							else
							{
								echo 'empty_password';
								return;
							}
						}
						else
						{
							echo 'empty_username';
							return;
						}
					}
					else
					{
						echo 'empty_username';
						return;
					}
				}
				else
				{
					echo 'register_disable';
					return;
				}
			}
		}
		public static function userRefClaim()
		{
			global $dbh, $lang;
			if (isset($_POST['claimdiamonds']))
			{
				if (User::userData('online') == 0)
				{
					$bankCount = $dbh->prepare("SELECT userid,diamonds FROM referrerbank WHERE userid = :userid");
					$bankCount->bindParam(':userid', $_SESSION['id']);
					$bankCount->execute();
					$bankCountData = $bankCount->fetch();
					if ($bankCountData['diamonds'] == 0)
					{
						return html::error($lang["MrefNoDia"]);
					}
					else
					{
						$addDiamondsRef = $dbh->prepare("
						UPDATE users SET 
						vip_points=vip_points + :diamonds 
						WHERE 
						id=:id
						");
						$addDiamondsRef->bindParam(':id', $_SESSION['id']);
						$addDiamondsRef->bindParam(':diamonds', $bankCountData['diamonds']);
						$addDiamondsRef->execute();
						$DiamondsCountRemove = $dbh->prepare("
						UPDATE referrerbank SET 
						diamonds = 0 
						WHERE 
						userid=:userid
						");
						$DiamondsCountRemove->bindParam(':userid', $_SESSION['id']);
						$DiamondsCountRemove->execute();
						return html::errorSucces($lang["MrefOnline"]);
					}	
				}
				else
				{
					return html::error('Je mag niet online zijn om je diamanten te claimen!');
				}
			}
		}
		Public static function editPassword()
		{
			global $dbh,$lang;
			if (isset($_POST['password']))
			{
				if (isset($_POST['oldpassword']) && !empty($_POST['oldpassword']))
				{
					if (isset($_POST['newpassword']) && !empty($_POST['newpassword']))
					{
						$stmt = $dbh->prepare("SELECT id, password, username FROM users WHERE id = :id");
						$stmt->bindParam(':id', $_SESSION['id']);
						$stmt->execute();
						$getInfo = $stmt->fetch();
						if (self::checkUser(filter($_POST['oldpassword']), $getInfo['password'], filter($getInfo['username'])))
						{
							if (strlen($_POST['newpassword']) >= 6)
							{
								$newPassword = self::hashed($_POST['newpassword']);
								$stmt = $dbh->prepare("
								UPDATE 
								users 
								SET password = 
								:newpassword 
								WHERE id = 
								:id
								");
								$stmt->bindParam(':newpassword', $newPassword); 
								$stmt->bindParam(':id', $_SESSION['id']); 
								$stmt->execute(); 
								return Html::errorSucces($lang["Ppasswordchanges"]);
							}
							else
							{
								return Html::error($lang["Ppasswordshort"]);
							}
						}
						else
						{
							return Html::error($lang["Poldpasswordwrong"]);
						}
					}
					else
					{
						return Html::error('Je nieuwe wachtwoord is leeg!');
					}
				}
				else
				{
					return Html::error('Oude wachtwoord is leeg!');
				}
			}
		}
		Public static function editEmail()
		{
			global $lang,$dbh;
			if (isset($_POST['account']))
			{
				if (isset($_POST['email']) && !empty($_POST['email']))
				{
					if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
					{
						if (!self::emailTaken($_POST['email']))
						{
							$stmt = $dbh->prepare("
							UPDATE 
							users 
							SET mail = 
							:newmail
							WHERE id = 
							:id
							");
							$stmt->bindParam(':newmail', $_POST['email']); 
							$stmt->bindParam(':id', $_SESSION['id']); 
							$stmt->execute(); 
							return Html::errorSucces($lang["Eemailchanges"]);
						}
						else
						{
							return Html::error($lang["Eemailexists"]);
						}
					}
					else
					{
						return Html::error($lang["Eemailnotallowed"]);
					}
				}
				else
				{
					return Html::error($lang["Enoemail"]);
				}
			}
		}
		Public static function editHotelSettings()
		{
			global $lang,$dbh;
			if (isset($_POST['hinstellingenv']))
			{
				$stmt = $dbh->prepare("
				UPDATE 
				users 
				SET ignore_invites = 
				:hinstellingenv
				WHERE id = 
				:id
				");
				$stmt->bindParam(':hinstellingenv', $_POST['hinstellingenv']); 
				$stmt->bindParam(':id', $_SESSION['id']); 
				$stmt->execute(); 
			}
			if (isset($_POST['hinstellingenl']))
			{
				$stmt = $dbh->prepare("
				UPDATE 
				users 
				SET allow_mimic = 
				:hinstellingenl
				WHERE id = 
				:id
				");
				$stmt->bindParam(':hinstellingenl', $_POST['hinstellingenl']); 
				$stmt->bindParam(':id', $_SESSION['id']); 
				$stmt->execute(); 
			}
			if (isset($_POST['hinstellingeno']))
			{
				$stmt = $dbh->prepare("
				UPDATE 
				users 
				SET hide_online = 
				:hinstellingeno
				WHERE id = 
				:id
				");
				$stmt->bindParam(':hinstellingeno', $_POST['hinstellingeno']); 
				$stmt->bindParam(':id', $_SESSION['id']); 
				$stmt->execute(); 
			}
			if (isset($_POST['hotelsettings']))
			{
				return Html::errorSucces($lang["Hchanges"]);
			}
		}
		Public static function editUsername()
		{
			global $lang,$dbh;
			if (isset($_POST['editusername']))
			{
				if(!User::userData('fbenable') == 1)
				{
					if(!self::userTaken($_POST['username']))
					{
						if(self::validName($_POST['username']))
						{
							$stmt = $dbh->prepare("UPDATE users SET username = :username, fbenable = '1' WHERE id = :id");
							$stmt->bindParam(':username', $_POST['username']); 
							$stmt->bindParam(':id', $_SESSION['id']); 
							$stmt->execute(); 
							header('Location: '.$config['hotelUrl'].'/me');
						}
						else
						{
							return Html::error($lang["Cusernameshort"]);
						}
					}
					else
					{
						return html::error($lang["Cusernameused"]);
					}
				}
				else
				{
					return html::error($lang["Cchangeno"]);
				}
			}
		}
	}																				