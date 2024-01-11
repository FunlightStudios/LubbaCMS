<?php
	include_once 'includes/header.php';
?>
<title><?= $config['hotelName'] ?>: <?= User::userData('username') ?></title>
<div class="center">
	<?php
		include_once 'includes/alerts.php';
	?>
<script src="<?= $config["hotelUrl"] ?>/templates/brain/style/js/custom.js"></script> <!-- IMPORT CUSTOM JS -->
<!-- START USERBOX -->
<div class="boxuser">
	<!-- START USERBOX LEFT SIDE -->
	<div class="leftbox">
		<img style="float:right; position:absolute; margin-left: -170px; margin-top: -10px:-webkit-filter: drop-shadow(0 1px 0 #FFFFFF) drop-shadow(0 -1px 0 #FFFFFF) drop-shadow(1px 0 0 #FFFFFF) drop-shadow(-1px 0 0 #FFFFFF)" src="<?= $config["hotelUrl"] ?>/templates/brain/style/images/me/teppich.gif"></img>
		<div class="userplate">
			<img src="/templates/brain/style/images/icons/online/<?= User::userData('online') ?>.png" style="float:left;padding: 5px;">
			<div class="useravatar">
				<div class="avatar" style="position:absolute; background-image:url(<?= $config['habboImagingUrl'] ?><?= User::userData('look') ?>&amp;direction=2&amp;head_direction=3&amp;action=crr=667&amp;gesture=sml);">
				</div>
			</div>
		</div>

		<div class="userstatsbox">
			<div style="color: #f8ef2b; background-image: url(/templates/brain/style/images/icons/crediticon.png);" class="userstats ">
				<?= User::userData('credits') ?> <?= $lang["Mcredits"] ?>
			</div>
		</div>

		<div class="userstatsbox">
			<div style="color: #e99bdc; background-image: url(/templates/brain/style/images/icons/duckicon.png);" class="userstats">
				<?= User::userData('activity_points') ?> <?= $lang["Mduckets"] ?>
			</div>
		</div>
		<div class="userstatsbox">
			<div style="color: #6caff4; background-image: url(/templates/brain/style/images/icons/diaicon.png);" class="userstats">
				<?= User::userData('vip_points') ?>
				<?= $lang["Mdiamond"] ?>
			</div>
		</div>



	</div>
	<!-- END USERBOX LEFT -->
<div class="usernamebox">
						<div class="username">
							<?= User::userData('username') ?> <!--<?= userlike(User::userData('id')) ?>-->
						</div>
					</div>
					<div class="usermottobox">
						<div class="usermotto">
							<?= User::userData('motto') ?>
						</div>
					</div>
	<!-- START USERBOX RIGHT -->
		<div class="rightbox">

				<div class="userfirst">

					<div class="userbuttonbox">
						<a href="/client" onclick="window.open('/client','new','toolbar=0,scrollbars=0,location=1,statusbar=1,menubar=0,resizable=1,width=1270,height=700');return false;">
						<div class="userbutton">
							<?= $lang["Mgoto"] ?>
						</div></a>
					</div>
				</div>

		</div>
		<!-- END USERBOX RIGHT -->
</div>

	<div class="columleft">
		<!-- News -->
		<div class="boxnews">
				<?php
					$sql = $dbh->prepare("SELECT id,title,image,shortstory FROM cms_news ORDER BY id DESC LIMIT 1");
					$sql->execute();
					while ($news = $sql->fetch())
					{
						echo'
						<div class="newsFirstImage" style="background-image: url('.filter($news["image"]).');">
						<div class="newsTitle">
						'.filter($news["title"]).'
						</div>
						<div class="newsTitleShort">
						'.filter($news["shortstory"]).'
						</div>
						<div class="newsTitleRead">
						<div class="newsTitleReadName">
						<a href="/news/'.filter($news["id"]).'">'.$lang["Mreadmore"].' »</a>
						</div>
						</div>



						</div>


						';
					}
				?>
			</div>
		<!-- Refferal -->
		<div style="padding: 10px;"class="box">
			<?= User::userRefClaim(); ?>
			<div class="refcountbox">
				<div class="refcountboxnumber">
					<?php
						$refCount = $dbh->prepare("SELECT refid FROM referrer WHERE refid = :refid");
						$refCount->bindParam(':refid', $_SESSION['id']);
						$refCount->execute();
						echo $refCount->RowCount();
					?>
				</div>
				<div class="refcountboxtext">
					<?= $lang["MrefUsers"] ?>
				</div>
			</div>
			<div class="refcountboxurlbox">
				<div class="refcountboxurltext">
					<a href="<?= $config['hotelUrl'] ?>/register/<?= User::userData('username') ?>"><?= $config['hotelUrl'] ?>/register/<?= User::userData('username') ?></a>
				</div>
				<div style="color:white;">
					<center><p><?= $lang["MrefLink"] ?></p></center>
				</div>
			</div>
			<div class="refdiabox">
				<?= $lang["MrefDiaBank"] ?>
				<div class="refdiaboxnumber">
					<?php
						$bankCount = $dbh->prepare("SELECT userid,diamonds FROM referrerbank WHERE userid = :userid");
						$bankCount->bindParam(':userid', $_SESSION['id']);
						$bankCount->execute();
						$bankCountData = $bankCount->fetch();
						if($bankCount->RowCount() == 0)
						{
							echo'0';
						}
						else
						{
							echo $bankCountData['diamonds'];
						}
					?>
					<img src="/templates/brain/style/images/icons/diaicon.png" align="">
				</div>
				<div class="refdiaboxtext">
					<form method="post">
						<input type="submit" class="submit" value="<?= $lang["MrefButton"] ?>" name="claimdiamonds" id="claimdiamonds" style="margin-top: 10px;">
					</form>
				</div>
			</div>
		</div>
		<!-- New Users -->
		<div style = "height: 169px;" class="box">
			<div class="lblue title">
				<?= $lang["Mnewinhabbo"] ?>
			</div>
			<div class="mainBox" style="float;left">
				<?php
					$sqlGetUsersByRankDev = $dbh->prepare("SELECT username,look FROM users ORDER BY ID DESC LIMIT 5");
					$sqlGetUsersByRankDev->execute();
					while ($getUsersDev = $sqlGetUsersByRankDev->fetch())
					{
					?>
					<div class="userNewBox">
						<a href="/home/<?= filter($getUsersDev['username']) ?>"><div class="userNew" style="background: url(<?= $config['habboImagingUrl'] ?><?= filter($getUsersDev['look']) ?>&direction=3&head_direction=3&action=wav&headonly=0); background-position: 15px 2px;width: 80px;float: left;background-repeat: no-repeat;"></div>
							<div class="userNewName">
							<?= filter($getUsersDev['username']) ?></a>
						</div>
					</div>
					<?php
					}
					echo "</div>";
				?>
			</div>
			<!-- Groups -->
			<div style = "height: 169px;" class="box">
				<div class="blue title">
					<?= $lang["Mtopgroupsinhabbo"] ?>
				</div>
				<div class="mainBox" style="float;left">
					<?php
						$getem = $dbh->prepare("SELECT *,COUNT(*) AS count FROM groups,group_memberships WHERE groups.id = group_memberships.group_id GROUP BY group_memberships.group_id ORDER BY count DESC LIMIT 5");
						$getem->execute();
						if ($getem->RowCount() > 0)
						{
							while ($row = $getem->fetch())
							{
								echo '<div class="groupboxbg">
								<a class="tooltip" href="#"><div class="userNew" style="background: url('. $config['groupBadgeURL'].''.filter($row['badge']).'); background-position: 30px 15px;width: 80px;float: left;background-repeat: no-repeat;"></div>
								<div class="userNewName">
								'. filter($row['name']).'
								<span>
								<h3>'.filter($row['name']).'</h3>
								<hr>
								'.filter($row['desc']).'
								</span>
								</a>
								</div>
								</div>';
							}
						}
						else
						{
							echo'<div class="groupboxbg">
							<a class="tooltip" href="#"><div class="userNew" style="background: url(/templates/brain/style/images/icons/ghostgroup.gif); background-position: 30px 15px;width: 80px;float: left;background-repeat: no-repeat;"></div>
							<div class="userNewName">
							Keine Gruppen
							<span>
							<h3>Keine Gruppen</h3>
							<hr>
							Sei der erste der eine Gruppe erstellt!
							</span>
							</a>
							</div>
							</div>';
						}
					?>
				</div>
			</div>
			<!-- Facebook -->
			<?php
				if($config['facebookEnable'] == true)
				{
				?>
				<div class="box">
					<div class="purple title">
						<?= $lang["Mfacebook"] ?>
					</div>
					<div class="mainBox" style="float;left">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s); js.id = id;
							js.src = "//connect.facebook.net/nl_NL/sdk.js#xfbml=1&version=v2.7&appId=183748235334636";
							fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
						<center><div class="fb-page" data-href="<?= $config['facebook'] ?>" data-tabs="timeline" data-width="500" data-height="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="<?= $config['facebook'] ?>" class="fb-xfbml-parse-ignore"><a href="<?= $config['facebook'] ?>"><?= $config['hotelName'] ?> Hotel - World Wide Community</a></blockquote></div></center>
					</div>
				</div>
				<?php
				}
			?>
		</div>
		<div class="columright">
		<!-- Radio Player -->
		<?php
				if($config['radioEnable'] == true)
				{
				?>
			<div class="box">
				<div class="yellow title">
					<?= $lang["LRdio"] ?>
				</div>
				<div class="mainBox" style="float;left">
					<div class="boxHeader"></div>

					<audio autoplay="true" controls="false" volume="0.5" preload="none">
					<source src="<?= $config["streamOGG"] ?>" type="audio/ogg">
					<source src="<?= $config["streamMp3"] ?>" type="audio/mpeg">
					</audio>

				</div>
			</div>
			<?php
				}
			?>
			<!-- User of the Week -->
			<?php
				if($config['userOfTheWeek'] == true)
				{
				?>
			<div class="box">
				<div class="title green">
					<?= $lang["Muotw"] ?>
				</div>
				<div class="mainBox" style="float;left">
					<div class="boxHeader"></div>
					<?= userOfTheWeek() ?>
				</div>
			</div>
			<?php
				}
			?>
			<!-- Active Rooms -->
			<div class="box">
				<div class="title orange">
					<?= $lang["Mnowinroom"] ?>
				</div>
				<div class="mainBox" style="float;left">
					<div class="boxHeader"></div>
					<div class="scroll" style="width:330px;overflow-y: auto;overflow-x: hidden;">
						<div id="roomcount"><?= $lang["mloading"] ?></div>
					</div>
				</div>
			</div>
			<!-- Relationsship status -->
			<div class="box">
				<div class="title blue">Freundschaftsstatus</div>
				<div class="mainBox" style="float;left">
					<div class="boxHeader"></div>
					<div class="friendlist" style="display: inline-block;">
						<?= friendList() ?>
					</div>
				</div>
			</div>
			<!-- Twitter -->
			<?php
				if($config['twitterEnable'] == true)
				{
				?>
				<div class="box">
					<div class="yellow title">
						<?= $lang["Mtwitter"] ?>
					</div>
					<a class="twitter-timeline" data-width="320" data-height="420" data-link-color="#FAB81E" href="<?= $config['twitter'] ?>">Tweets by <?= $config['hotelName'] ?></a> <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
				</div>
				<?php
				}
			?>
		</div>
		<?php
			include_once 'includes/footer.php';
		?>
	</div>
</div>
</body>
</html>
