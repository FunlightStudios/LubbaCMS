<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * Display friend list with relationship types
 * PHP 8+ compatible with null safety
 */
function friendList(): void {
	global $dbh, $config, $lang;
	
	echo '<link rel="stylesheet" href="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/css/simplefriends.css?v=2" type="text/css">';
	
	// Helper function to safely filter values
	$safeFilter = function($value): string {
		return filter((string)($value ?? ''));
	};
	
	//INFORMATIE VAN TYPE 1
	$getRelations1 = $dbh->prepare("SELECT * FROM user_relationships WHERE user_id = :id AND type = '1' ORDER BY RAND()");
	$getRelations1->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
	$getRelations1->execute();
	$infoRelations1 = $getRelations1->fetch();
	$infoRelationsNum = $getRelations1->rowCount();
	
	$infoFriends = null;
	if ($infoRelations1 && isset($infoRelations1['target'])) {
		$getUser = $dbh->prepare("SELECT id,username,look,online FROM users WHERE id = :targetId");
		$getUser->bindParam(':targetId', $infoRelations1['target'], PDO::PARAM_INT);
		$getUser->execute();
		$infoFriends = $getUser->fetch();
	}
	
	$friend_online = "<span class='friend_online'>offline</span>";
	if ($infoFriends && isset($infoFriends['online']) && $infoFriends['online'] == '1') {
		$friend_online = "<span class='friend_online'>online</span>";
	}
	
	if ($infoRelationsNum == 0) {
			echo '
			<div class="friend_1" style="padding: 10px;">
			'.$lang['SFnofriends']. '
			<img src="'.$config['hotelUrl'].'/templates/brain/style/images/icons/iconlove.png" class="friend_icon" style="margin-top: -3px;float: right;">
			</div>
			';
	} else {
		if ($infoRelationsNum == 1) {
			$infoNumtext = $lang['SFmakemorefriends'] ?? 'Make more friends!';
		} else {
			$infoRelationsNum = $infoRelationsNum - 1;
			$infoNumtext = "You have <b>" . $infoRelationsNum . "</b> friend more in the category";
		}
		
		$friendLook = $safeFilter($infoFriends['look'] ?? '');
		$friendUsername = htmlspecialchars($infoFriends['username'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
		
		echo '
		<div class="friend_1">
		<table>
		<tr>
		<td>
		<div class="circle_friend">
		<div class="friend_head" style="background: url(https://www.habbo.com/habbo-imaging/avatarimage?figure=' . $friendLook . '&head_direction=2&action=wav&headonly=1)">
		</div>
		</div>
		</td>
		<td>
		<img src="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/images/icons/iconlove.png" class="friend_icon">
		</td>
		<td>
		' . $friendUsername . '
		</td>
		<td style="width: 100%;">
		' . $friend_online . '
		</td>
		</tr>
		</table>
		<div class="numRows_friend">
		' . $infoNumtext . '
		</div>
		</div>
		';
	}
		//INFORMATIE VAN TYPE 2
	$getRelations2 = $dbh->prepare("SELECT * FROM user_relationships WHERE user_id = :id AND type = '2' ORDER BY RAND()");
	$getRelations2->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
	$getRelations2->execute();
	$infoRelations2 = $getRelations2->fetch();
	$infoRelationsNum2 = $getRelations2->rowCount();
	
	$infoFriends2 = null;
	if ($infoRelations2 && isset($infoRelations2['target'])) {
		$getUser2 = $dbh->prepare("SELECT id,username,look,online FROM users WHERE id = :targetId");
		$getUser2->bindParam(':targetId', $infoRelations2['target'], PDO::PARAM_INT);
		$getUser2->execute();
		$infoFriends2 = $getUser2->fetch();
	}
	
	$friend_online2 = "<span class='friend_online'>offline</span>";
	if ($infoFriends2 && isset($infoFriends2['online']) && $infoFriends2['online'] == '1') {
		$friend_online2 = "<span class='friend_online'>online</span>";
	}
	
	if ($infoRelationsNum2 == 0) {
		echo '
		<div class="friend_2" style="padding: 10px;">
		Du hast keine Freunde in dieser Kategorie!
		<img src="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/images/icons/iconbest.png" class="friend_icon" style="margin-top: -3px;float: right;">
		</div>
		';
	} else {
		if ($infoRelationsNum2 == 1) {
			$infoNumtext2 = $lang['SFmakemorefriends'] ?? 'Make more friends!';
		} else {
			$infoRelationsNum2 = $infoRelationsNum2 - 1;
			$infoNumtext2 = "You have <b>" . $infoRelationsNum2 . "</b> friends more in the category";
		}
		
		$friendLook2 = $safeFilter($infoFriends2['look'] ?? '');
		$friendUsername2 = htmlspecialchars($infoFriends2['username'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
		
		echo '
		<div class="friend_2">
		<table>
		<tr>
		<td>
		<div class="circle_friend">
		<div class="friend_head" style="background: url(https://avatar-retro.com/habbo-imaging/avatarimage?figure=' . $friendLook2 . '&head_direction=2&action=wav&headonly=1)">
		</div>
		</div>
		</td>
		<td>
		<img src="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/images/icons/iconbest.png" class="friend_icon">
		</td>
		<td>
		' . $friendUsername2 . '
		</td>
		<td style="width: 100%;">
		' . $friend_online2 . '
		</td>
		</tr>
		</table>
		<div class="numRows_friend">
		' . $infoNumtext2 . '
		</div>
		</div>
		';
	}
		//INFORMATIE VAN TYPE 3
	$getRelations3 = $dbh->prepare("SELECT * FROM user_relationships WHERE user_id = :id AND type = '3' ORDER BY RAND()");
	$getRelations3->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
	$getRelations3->execute();
	$infoRelations3 = $getRelations3->fetch();
	$infoRelationsNum3 = $getRelations3->rowCount();
	
	$infoFriends3 = null;
	if ($infoRelations3 && isset($infoRelations3['target'])) {
		$getUser3 = $dbh->prepare("SELECT id,username,look,online FROM users WHERE id = :targetId");
		$getUser3->bindParam(':targetId', $infoRelations3['target'], PDO::PARAM_INT);
		$getUser3->execute();
		$infoFriends3 = $getUser3->fetch();
	}
	
	$friend_online3 = "<span class='friend_online'>offline</span>";
	if ($infoFriends3 && isset($infoFriends3['online']) && $infoFriends3['online'] == '1') {
		$friend_online3 = "<span class='friend_online'>online</span>";
	}
	
	if ($infoRelationsNum3 == 0) {
		echo '
		<div class="friend_3" style="padding: 10px;">
		' . ($lang['SFnofriends'] ?? 'No friends in this category!') . '
		<img src="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/images/icons/iconheat.png" class="friend_icon" style="margin-top: -3px;float: right;">
		</div>
		';
	} else {
		if ($infoRelationsNum3 == 1) {
			$infoNumtext3 = $lang['SFmakemorefriends'] ?? 'Make more friends!';
		} else {
			$infoRelationsNum3 = $infoRelationsNum3 - 1;
			$infoNumtext3 = "You have <b>" . $infoRelationsNum3 . "</b> friends more in the category";
		}
		
		$friendLook3 = $safeFilter($infoFriends3['look'] ?? '');
		$friendUsername3 = htmlspecialchars($infoFriends3['username'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
		
		echo '
		<div class="friend_3">
		<table>
		<tr>
		<td>
		<div class="circle_friend">
		<div class="friend_head" style="background: url(https://avatar-retro.com/habbo-imaging/avatarimage?figure=' . $friendLook3 . '&head_direction=2&action=wav&headonly=1)">
		</div>
		</div>
		</td>
		<td>
		<img src="' . htmlspecialchars($config['hotelUrl']) . '/templates/brain/style/images/icons/iconheat.png" class="friend_icon">
		</td>
		<td>
		' . $friendUsername3 . '
		</td>
		<td style="width: 100%;">
		' . $friend_online3 . '
		</td>
		</tr>
		</table>
		<div class="numRows_friend">
		' . $infoNumtext3 . '
		</div>
		</div>
		';
	}
}
