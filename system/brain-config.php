<?php
	if(!defined('BRAIN_CMS'))
	{
		die('Sorry but you cannot access this file!');
	}

	/*
		  _           _     _            _____ __  __  _____   ____       _          ___  ____          ___  _____ _
		 | |         | |   | |          / ____|  \/  |/ ____| |  _ \     | |        |__ \|___ \        |__ \| ____| |
		 | |    _   _| |__ | |__   __ _| |    | \  / | (___   | |_) | ___| |_ __ _     ) | __) |_      __ ) | |__ | |__
		 | |   | | | | '_ \| '_ \ / _` | |    | |\/| |\___ \  |  _ < / _ \ __/ _` |   / / |__ <\ \ /\ / // /|___ \| '_ \
		 | |___| |_| | |_) | |_) | (_| | |____| |  | |____) | | |_) |  __/ || (_| |  / /_ ___) |\ V  V // /_ ___) | |_) |
		 |______\__,_|_.__/|_.__/ \__,_|\_____|_|  |_|_____/  |____/ \___|\__\__,_| |____|____/  \_/\_/|____|____/|_.__/

		LubbaCMS based on latest BrainCMS by Retroripper & PlusEMU by Sledmore
		LubbaEMU edited by Jerry from Swiss Retro Industry.
		We love Habbo so we continue development, we also think the latest nice HABBO Version is
		REV: PRODUCTION-201701242205-837386173. More than 100 Companys will continue the Flash development (ex. Flash Browse, W3 & many more),
		so you can use SWF Habbo Versions in 2023+. Also you can setup the New Nitro Client to run your HABBO SWF in any Browser,
		The original Flash Client & Nitro can run paralell, just follow the Instructions in Readme.md or at SwissRetroIndustry.tk.
		Since 07.2023 you can easy create an EXE Client from your Hotel, Downloads & Instructions at SwissRetroIndustry.tk
		Special thanks to Funlight Studios & Lukadora, i learned to love the Habbo development from him.

		If you have any Issues or Bugs please view http://SwissRetroIndustry.ch/LubbaCMS/knownissues to check if we know this issue now,
		if not please report your issues with the Bug-Panel.

									THIS CMS IS OFFICIALLY USED BY LUBBAHOTEL.TK    */


	/* Database Setting */
	$db['host'] = '127.0.0.1'; //Mysql's Host
	$db['port'] = '3306'; //Mysql's port
	$db['user'] = "root"; //Mysql's user
	$db['pass'] = ''; //Mysql's password
	$db['db'] = "lubba"; //Mysql's database

	/* Emu Settings */
	$config['hotelEmu'] = 'plusemu'; // plusemu // arcturus (LubbaDB for LubbaEMU using 'plusemu')

	/* Flash Client Setting */
	$hotel['emuHost'] = "127.0.0.1"; //IP of VPS//IP of Proxy
	$hotel['emuPort'] = "30000";  //Port of VPS//Port of Proxy
	$hotel['staffCheckClient'] = true; //Enable the staff pin in the client (true) or disable it (false)
	$hotel['staffCheckClientMinimumRank'] = 5; //Minium staff rank to get the staff pin in the client
	$hotel['homeRoom'] = '20'; //Set the start room when you get in the hotel
	$hotel['external_Variables'] = ''.$_SERVER['DOCUMENT_ROOT'].'/swf/gamedata/external_variables.txt';
	$hotel['external_Variables_Override'] = "http://127.0.0.1/swf/gamedata/override/external_override_variables.txt";
	$hotel['external_Texts'] = "http://127.0.0.1/swf/gamedata/external_flash_texts.txt";
	$hotel['external_Texts_Override'] = "http://127.0.0.1/swf/gamedata/override/external_flash_override_texts.txt";
	$hotel['productdata'] = "http://127.0.0.1/swf/gamedata/productdata.txt";
	$hotel['furnidata'] = "http://127.0.0.1/swf/gamedata/furnidata.xml";
	$hotel['figuremap'] = "http://127.0.0.1/swf/gamedata/figuremap.xml";
	$hotel['figuredata'] = "http://127.0.0.1/swf/gamedata/figuredata.xml";
	$hotel['swfFolder'] = "http://127.0.0.1/swf/gordon/PRODUCTION-201701242205-837386173/";
	$hotel['swfFolderSwf'] = "http://127.0.0.1/swf/gordon/PRODUCTION-201701242205-837386173/Habbo.swf";
	$hotel['onlineCounter'] = true; // Enable the user count in the client.
	$hotel['diamonds.enabled'] = true; // Enable diamonds in the hotel. (recommend)

	/* Website Setting */
	$config['hotelUrl'] = "http://127.0.0.1";//Address of your hotel. Does not end with a "/"
	$config['skin'] = "brain"; //Skin/template of your website
	$config['lang'] = "de"; //Language of your website de/en/nl/es (Only DE & EN are complete, to get the ENG Version of the Client check SwissRetroIndustry.ch/LubbaCMS/assets/engclient)
	$config['hotelName'] = "Lubba"; //Name of your hotel
	$config['hotelFullName'] = "Lubba Hotel"; //Name of your hotel just with Hotel xD
	$config['favicon'] = "http://127.0.0.1/templates/brain/style/images/logo/favicon/favicon.ico";
	$config['HkCheckCMSupdate'] = true; //Automatic check Updates from LubbaCMS in HK. WORKS ONLY WHEN YOU DON`T CHANGE "lubbaversion"
	$config['staffCheckHk'] = false; //Enable the staff pin in the housekeeping (true) or disable it (false)
	$config['staffCheckHkMinimumRank'] = 3; //Minium staff rank to get the staff pin in the housekeeping
	$config['maintenance'] = false; //Enable the maintenance of your website (true) or disable it (false)
	$config['maintenancekMinimumRankLogin'] = 3; //Minium staff rank to login when the website is in maintenance
	$config['groupBadgeURL'] = "http://127.0.0.1/swf/habbo-imaging/badge.php?badge=";
	$config['badgeURL'] = "http://127.0.0.1/swf/c_images/album1584/";
	$config['userLikeEnable'] = true; // Enable user likes
	$config['newsCommandEnable'] = true; //Enable news commands
	$config['newsCommandFilter'] = true; //Enable wordfilter on news commands (the filter use the db tabels wordfilter and wordfilter_characters)
	$config['alertReferrer'] = true;
	$config['alert'] = 'Lubba wurde 2011 von Randy & Flimmerherzi eröffnet!'; //Alert message. If you don't want a alert, you fill in 'nomessage' or you do leave it blank.
	$config['lubbaversion'] = 'Beta 280123'; // Please do not change.
	$config['sitetitle'] = 'Treffe alte und neue Frunde nur im Lubba Hotel!'; //Set a custom Meta-title for Index
	$config['Itext1header'] = 'CMS & Emulator'; // Index textbox 1 header
	$config['Itext1'] = 'Lubba Hotel läuft mit der LubbaCMS basierend auf Brain(R63b Flash), als Emulator kommt ein editierter PlusEMU zum einsatz. Es wurde bereits einiges gefixt und Erweitert wir werden noch einiges mehr an Arbeit reinstecken um das beste erlebnis im Lubba Hotel bieten zu können.<br><br><br><br><br><br>'; //Index textbox 1 text
	$config['Itext2header'] = 'Wöchentliche Events'; //Index textbox 2 header
	$config['Itext2'] = 'Im Lubba Hotel, organisieren wir wöchentlich ein offizielles Event direkt vom Lubba Team! Weitere Events durch die Woche gibt es natürlich durch unsere treuen Lubba-User!<br><br><br><br>'; //Index textbox 2 text
	$config['Itext3header'] = 'Adobe Flash Player'; //Index textbox 3 header
	$config['Itext3'] = 'Adobe hatt am 31.12.2020 den Flash Player ausgeschaltet!<br>Wie wir alle wissen benötigen wir den Flash Player dringendst um in unserem Hotel zu spielen. Was also Tun? Natürlich gibt es Alternativen. Wir empfehlen: <a style="color:#FF0000; download="Chrome"; href="http://127.0.0.1/downloads/ChromeFlash.zip">Chrome Portable mit flash!</a><br><br><br>'; //Index textbox 3 text
	$config['userOfTheWeek'] = true; //Use the User of the Week feature (Set the user in the Housekeeping)
	$config['habboImagingUrl'] = 'https://imager.hubba.cc/avatarimage?figure='; //Your Habbo imaging URL (ends always with ../avatarimage?figure=)

	/* Radio Setting */
	$config['radioEnable'] = true; //Enable/Disable Radio
	$config['streamOGG'] = 'http://icepool.silvacast.com/DEFJAY.mp3'; //If your Streamlink is an OGG File
	$config['streamMp3'] = 'http://icepool.silvacast.com/DEFJAY.mp3'; //If your Streamlink is an MP3 File
	// More Infos about Radio Stream link in readme.md

	/*Staff apply */
	$config['EnableStaffApply'] = true; // Enable/Disable Staff Apply
	$config['EnableX'] = true; //Do you search helpers?
	$config['EnableMod'] = true; //Do you search Moderators?
	$config['EnableAdmin'] = true; //Do you search Administators?
	$config['EnableTech'] = true; //Do you search Technics/Devs?

	/* Adminpanel settings */
	$config['adminUseinfo'] = '
		Das Housekeeping soll nicht dazu dienen, herumzualbern, sondern es einfacher zu machen, die Dinge zu verwalten. Wenn Sie dabei erwischt werden, wie Sie im Housekeeping von Lubba herumalbern, werden Sie entlassen!
		<br>
		<br>
		Lubba Hotel kann nachschauen wer sich ins Housekeeping eingeloggt hatt und welche Dinge ausgeführt wurden.';


	/* Facebook Login Settings (i think this Method dont work anymore)
		You need a Facebook app for this to work go to
		https://developers.facebook.com/apps/ */

	$config['facebookLogin'] = false; //Enable the Facebook login (true) or disable it (false)
	$config['facebookAPPID'] = '334162590sdaf292528'; //Facebook AppID
	$config['facebookAPPSecret'] = 'ce2504ff5adsfa3ff7a6a2fa6d984cd8836'; //Facebook secret Key

	/* Email Settings (to send password reset Email) smtp only*/
	$email['mailServerHost'] = 'smtp.gmail.com'; //E-Mail service smtp Address
	$email['mailServerPort'] = 587; //E-Mail service server port (smtp Port)
	$email['SMTPSecure'] = 'TLS'; //Security
	$email['mailUsername'] = 'gmail@gmail.com'; //E-Mail address
	$email['mailPassword'] = '*****'; //E-Mail service password
	$email['mailLogo'] = 'http://127.0.0.1/templates/brain/style/images/logo/logo.png'; //E-Mail Logo
	$email['mailTemplate'] = '/system/app/plugins/PHPmailer/temp/resetpassword.html'; //E-Mail Template

	/* Social settings */
	$config['facebook'] = 'https://www.facebook.com/Habbo/';
	$config['facebookEnable'] = false;
	$config['twitter'] = 'https://twitter.com/Habbo';
	$config['twitterEnable'] = false;
	$config['discord'] = '';
	$config['discordEnable'] = false;

	/* Register Setting */
	$config['startMotto'] = "Neu hier!"; //Regsiter start motto
	$config['credits']	= "10000"; //Register start Credits
	$config['duckets']	= "20000"; //REgister start Duckets
	$config['diamonds']	= "10"; //Register start Diamonds
	$config['diamondsRef']	= "10"; //How much Diamonds the reffer user get
	$config['registerEnable'] = true;

	/* Google recaptcha Site Key  (i think this Method dont work anymore)
	   Go to this website to create a recaptcha Site Key: https://www.google.com/recaptcha/intro/index.html	*/
	$config['recaptchaSiteKey'] = "6Ler2DMkAAAAADYL0M1cjAjU4czWonKANGK2_XkQ"; //Google recaptcha Key
	$config['recaptchaSiteKeyEnable'] = false; //Recaptcha enable/disable

	/* Buy VIP Settings */
	$config['vipCost'] = "100";
	$config['vipRankToGet'] = "1"; //Subscription Rank (1:SilverVIP, 2:GoldVIP, 3:EventStaff)
	$config['vipBadge'] = "vip";

	/* DO NOT CHANGE ANYTHING IF YOU DON'T KNOW WHAT YOU DO*/
	switch($config['hotelEmu'])
	{
		case "arcturus":
		$emuUse['user_wardrobe']  = 'users_wardrobe ';
		$emuUse['ip_last'] = 'ip_current';
		$emuUse['respect'] = 'respects_received';
		$emuUse['user_stats'] = 'users_settings';
		$emuUse['user_stats_user_id'] = 'user_id';
		$emuUse['OnlineTime'] = 'online_time';
		break;
		case "plusemu":
		$emuUse['user_wardrobe']  = 'user_wardrobe ';
		$emuUse['ip_last'] = 'ip_last';
		$emuUse['respect'] = 'Respect';
		$emuUse['user_stats'] = 'user_stats';
		$emuUse['user_stats_user_id'] = 'id';
		$emuUse['OnlineTime'] = 'OnlineTime';
		break;
		default:
		//Nothing
		break;
	}
?>
