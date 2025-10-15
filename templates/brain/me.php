<?php
	include_once 'includes/header.php';
?>
<title><?= $config['hotelName'] ?>: <?= User::userData('username') ?></title>
<div class="center">
	<?php include_once 'includes/alerts.php'; ?>
	
	<!-- Modern Profile Header -->
	<div class="profile-card fade-in" style="margin-bottom: 24px;">
		<div class="profile-header">
			<div class="profile-avatar-wrapper">
				<div class="profile-avatar" style="background-image:url(<?= $config['habboImagingUrl'] ?><?= User::userData('look') ?>&direction=2&head_direction=3&action=wav&gesture=sml);"></div>
			</div>
		</div>
		<div class="profile-body">
			<div class="profile-name">
				<?= User::userData('username') ?>
				<?php if(User::userData('online') == '1'): ?>
					<span class="badge badge-success" style="font-size: 12px; margin-left: 8px;">
						<i class="fas fa-circle" style="font-size: 8px;"></i> Online
					</span>
				<?php endif; ?>
			</div>
			<div class="profile-motto">"<?= User::userData('motto') ?>"</div>
			
			<div class="profile-stats">
				<div class="profile-stat">
					<span class="profile-stat-value" style="color: #fbbf24;">
						<i class="fas fa-coins"></i> <?= User::userData('credits') ?>
					</span>
					<span class="profile-stat-label"><?= $lang["Mcredits"] ?? 'Credits' ?></span>
				</div>
				<div class="profile-stat">
					<span class="profile-stat-value" style="color: #ec4899;">
						<i class="fas fa-gem"></i> <?= User::userData('activity_points') ?>
					</span>
					<span class="profile-stat-label"><?= $lang["Mduckets"] ?? 'Duckets' ?></span>
				</div>
				<div class="profile-stat">
					<span class="profile-stat-value" style="color: #3b82f6;">
						<i class="fas fa-diamond"></i> <?= User::userData('vip_points') ?>
					</span>
					<span class="profile-stat-label"><?= $lang["Mdiamond"] ?? 'Diamonds' ?></span>
				</div>
			</div>
			
			<div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
				<a href="/client" onclick="window.open('/client','new','toolbar=0,scrollbars=0,location=1,statusbar=1,menubar=0,resizable=1,width=1270,height=700');return false;" class="btn btn-primary">
					<i class="fas fa-play"></i> <?= $lang["Hgoto"] ?? 'Zum Hotel' ?>
				</a>
				<a href="/settingspassword" class="btn btn-info">
					<i class="fas fa-cog"></i> <?= $lang["Naccountsettings"] ?? 'Einstellungen' ?>
				</a>
			</div>
		</div>
	</div>

	<!-- Modern Two Column Layout -->
	<div class="content-wrapper">
		<div class="columleft">
		<!-- Latest News -->
		<?php
			$sql = $dbh->prepare("SELECT id,title,image,shortstory FROM cms_news ORDER BY id DESC LIMIT 1");
			$sql->execute();
			if ($news = $sql->fetch()):
		?>
		<div class="news-card fade-in" style="margin-bottom: 24px;">
			<div class="news-image" style="background-image: url('<?= htmlspecialchars($news["image"]) ?>');">
				<div class="news-badge">
					<i class="fas fa-newspaper"></i> Neueste News
				</div>
			</div>
			<div class="news-content">
				<div class="news-date">
					<i class="fas fa-clock"></i>
					<?= date('d.m.Y H:i') ?> Uhr
				</div>
				<h3 class="news-title"><?= htmlspecialchars($news["title"]) ?></h3>
				<p class="news-excerpt"><?= htmlspecialchars($news["shortstory"]) ?></p>
				<a href="/news/<?= $news["id"] ?>" class="btn btn-primary" style="width: 100%;">
					<i class="fas fa-arrow-right"></i> <?= $lang["Mreadmore"] ?? 'Weiterlesen' ?>
				</a>
			</div>
		</div>
		<?php endif; ?>
		<!-- Referral System -->
		<div class="box fade-in">
			<div class="title blue">
				<i class="fas fa-users"></i> <?= $lang["MrefLink"] ?? 'Freunde werben' ?>
			</div>
			<div class="mainBox">
				<?= User::userRefClaim(); ?>
				
				<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; width: 100%;">
					<div class="info-box">
						<div class="info-box-icon success">
							<i class="fas fa-user-friends"></i>
						</div>
						<div class="info-box-content">
							<div class="info-box-text"><?= $lang["MrefUsers"] ?? 'Geworbene Freunde' ?></div>
							<div class="info-box-number">
								<?php
									$refCount = $dbh->prepare("SELECT refid FROM referrer WHERE refid = :refid");
									$refCount->bindParam(':refid', $_SESSION['id'], PDO::PARAM_INT);
									$refCount->execute();
									echo $refCount->rowCount();
								?>
							</div>
						</div>
					</div>
					<div class="info-box">
						<div class="info-box-icon primary">
							<i class="fas fa-diamond"></i>
						</div>
						<div class="info-box-content">
							<div class="info-box-text"><?= $lang["MrefDiaBank"] ?? 'Diamanten Bank' ?></div>
							<div class="info-box-number">
								<?php
									$bankCount = $dbh->prepare("SELECT userid,diamonds FROM referrerbank WHERE userid = :userid");
									$bankCount->bindParam(':userid', $_SESSION['id'], PDO::PARAM_INT);
									$bankCount->execute();
									$bankCountData = $bankCount->fetch();
									echo ($bankCount->rowCount() == 0) ? '0' : $bankCountData['diamonds'];
								?>
							</div>
						</div>
					</div>
				</div>
				
				<div style="background: var(--bg-primary); padding: 16px; border-radius: var(--radius-md); border: 2px dashed var(--border-color);">
					<label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-secondary); font-size: 13px;">
						<i class="fas fa-link"></i> Dein Werbe-Link:
					</label>
					<input type="text" 
						   value="<?= $config['hotelUrl'] ?>/register/<?= User::userData('username') ?>" 
						   readonly 
						   onclick="this.select(); document.execCommand('copy'); alert('Link kopiert!');"
						   style="cursor: pointer; font-family: monospace; font-size: 13px;"
						   title="Klicken zum Kopieren">
					<small style="display: block; margin-top: 8px; color: var(--text-light); font-size: 12px;">
						<i class="fas fa-info-circle"></i> Klicke auf den Link um ihn zu kopieren
					</small>
				</div>
				
				<form method="post" style="margin-top: 16px;">
					<button type="submit" name="claimdiamonds" class="btn btn-success" style="width: 100%;">
						<i class="fas fa-diamond"></i> <?= $lang["MrefButton"] ?? 'Diamanten abholen' ?>
					</button>
				</form>
			</div>
		</div>
		
		<!-- New Users -->
		<div class="box fade-in">
			<div class="title" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
				<i class="fas fa-user-plus"></i> <?= $lang["Mnewinhabbo"] ?? 'Neue Benutzer' ?>
			</div>
			<div class="mainBox">
				<div class="online-users-grid">
					<?php
						$sqlGetUsersByRankDev = $dbh->prepare("SELECT username, look FROM users ORDER BY id DESC LIMIT 6");
						$sqlGetUsersByRankDev->execute();
						while ($getUsersDev = $sqlGetUsersByRankDev->fetch()):
					?>
					<a href="/home/<?= htmlspecialchars($getUsersDev['username']) ?>" class="online-user">
						<div class="online-user-avatar" style="background-image: url('<?= $config['habboImagingUrl'] ?><?= htmlspecialchars($getUsersDev['look']) ?>&direction=3&head_direction=3&action=wav');"></div>
						<div class="online-user-name"><?= htmlspecialchars($getUsersDev['username']) ?></div>
					</a>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
		<!-- Top Groups -->
		<div class="box fade-in">
			<div class="title blue">
				<i class="fas fa-users"></i> <?= $lang["Mtopgroupsinhabbo"] ?? 'Top Gruppen' ?>
			</div>
			<div class="mainBox">
				<div class="list-group">
					<?php
						$getem = $dbh->prepare("SELECT groups.*, COUNT(*) AS member_count FROM groups 
												INNER JOIN group_memberships ON groups.id = group_memberships.group_id 
												GROUP BY group_memberships.group_id 
												ORDER BY member_count DESC LIMIT 5");
						$getem->execute();
						if ($getem->rowCount() > 0):
							while ($group = $getem->fetch()):
					?>
					<div class="list-group-item" style="display: flex; align-items: center; gap: 16px;">
						<div style="width: 60px; height: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--bg-primary); border-radius: var(--radius-md); padding: 5px;">
							<img src="<?= htmlspecialchars($config['groupBadgeURL'] . $group['badge']) ?>" 
								 alt="<?= htmlspecialchars($group['name']) ?>" 
								 style="max-width: 100%; max-height: 100%; object-fit: contain;"
								 onerror="this.src='/templates/brain/style/images/icons/ghostgroup.gif'">
						</div>
						<div style="flex: 1; min-width: 0;">
							<div style="font-weight: 600; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
								<?= htmlspecialchars($group['name']) ?>
							</div>
							<div style="font-size: 13px; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
								<?= htmlspecialchars(substr($group['desc'] ?? 'Keine Beschreibung', 0, 50)) ?>...
							</div>
						</div>
						<div style="text-align: right; flex-shrink: 0;">
							<div style="font-size: 20px; font-weight: 700; color: #667eea;"><?= $group['member_count'] ?></div>
							<div style="font-size: 11px; color: var(--text-light);">Mitglieder</div>
						</div>
					</div>
					<?php 
							endwhile;
						else:
					?>
					<div class="list-group-item" style="text-align: center; padding: 40px 20px;">
						<div style="font-size: 48px; margin-bottom: 16px;">👻</div>
						<div style="font-weight: 600; margin-bottom: 8px;">Keine Gruppen</div>
						<div style="font-size: 13px; color: var(--text-secondary);">Sei der erste der eine Gruppe erstellt!</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!-- Facebook -->
		<?php if($config['facebookEnable'] == true): ?>
		<div class="box fade-in">
			<div class="title" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
				<i class="fab fa-facebook"></i> <?= $lang["Mfacebook"] ?? 'Facebook' ?>
			</div>
			<div class="mainBox">
				<div id="fb-root"></div>
				<script>
				(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.7";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				</script>
				<div class="fb-page" data-href="<?= htmlspecialchars($config['facebook']) ?>" 
					 data-tabs="timeline" 
					 data-width="100%" 
					 data-height="400" 
					 data-small-header="false" 
					 data-adapt-container-width="true" 
					 data-hide-cover="false" 
					 data-show-facepile="true">
				</div>
			</div>
		</div>
		<?php endif; ?>
		</div>
		<div class="columright">
		<!-- Radio Player -->
		<?php if($config['radioEnable'] == true): ?>
		<link rel="stylesheet" href="<?= $config['hotelUrl'] ?>/templates/brain/style/css/radio-player.css">
		<div class="box fade-in">
			<div class="mainBox" style="padding: 0;">
				<div class="radio-player-container">
					<div class="radio-header">
						<div class="radio-title">
							<span class="radio-icon">🎵</span>
							<?= $lang["LRdio"] ?? 'Lubba Hotel Radio' ?>
						</div>
						<div class="radio-status">
							<span class="status-indicator"></span>
							<span class="status-text">Live</span>
						</div>
					</div>
					
					<div class="radio-controls">
						<div class="custom-audio-player">
							<button class="play-pause-btn" id="playPauseBtn" onclick="togglePlay()">
								<span id="playIcon">▶</span>
							</button>
							
							<div class="volume-control-wrapper">
								<span class="volume-icon" onclick="toggleMute()">🔊</span>
								<input type="range" min="0" max="100" value="50" class="volume-slider" id="volumeSlider" oninput="changeVolume(this.value)">
								<span class="volume-percentage" id="volumePercentage">50%</span>
							</div>
						</div>
					</div>
					
					<div class="radio-info">
						<div class="now-playing">🎧 Jetzt läuft: Lubba Radio Stream</div>
						<div class="equalizer" id="equalizer">
							<div class="equalizer-bar"></div>
							<div class="equalizer-bar"></div>
							<div class="equalizer-bar"></div>
							<div class="equalizer-bar"></div>
							<div class="equalizer-bar"></div>
						</div>
					</div>
					
					<audio id="radioAudio" preload="none">
						<source src="<?= $config["streamOGG"] ?>" type="audio/ogg">
						<source src="<?= $config["streamMp3"] ?>" type="audio/mpeg">
					</audio>
				</div>
			</div>
		</div>
		
		<script>
		const audio = document.getElementById('radioAudio');
		const playPauseBtn = document.getElementById('playPauseBtn');
		const playIcon = document.getElementById('playIcon');
		const volumeSlider = document.getElementById('volumeSlider');
		const volumePercentage = document.getElementById('volumePercentage');
		const equalizer = document.getElementById('equalizer');
		
		// Set initial volume
		audio.volume = 0.5;
		
		function togglePlay() {
			if (audio.paused) {
				audio.play();
				playIcon.textContent = '⏸';
				playPauseBtn.classList.add('playing');
				equalizer.style.display = 'flex';
			} else {
				audio.pause();
				playIcon.textContent = '▶';
				playPauseBtn.classList.remove('playing');
				equalizer.style.display = 'none';
			}
		}
		
		function changeVolume(value) {
			audio.volume = value / 100;
			volumePercentage.textContent = value + '%';
		}
		
		function toggleMute() {
			if (audio.volume > 0) {
				audio.volume = 0;
				volumeSlider.value = 0;
				volumePercentage.textContent = '0%';
			} else {
				audio.volume = 0.5;
				volumeSlider.value = 50;
				volumePercentage.textContent = '50%';
			}
		}
		
		// Hide equalizer initially
		equalizer.style.display = 'none';
		</script>
		<?php endif; ?>
		
		<!-- User of the Week -->
		<?php if($config['userOfTheWeek'] == true): ?>
		<div class="box fade-in">
			<div class="title green">
				<i class="fas fa-star"></i> <?= $lang["Muotw"] ?? 'User der Woche' ?>
			</div>
			<div class="mainBox">
				<?= userOfTheWeek() ?>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Active Rooms -->
		<div class="box fade-in">
			<div class="title" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white;">
				<i class="fas fa-door-open"></i> <?= $lang["Mnowinroom"] ?? 'Aktive Räume' ?>
			</div>
			<div class="mainBox">
				<div style="max-height: 300px; overflow-y: auto;">
					<div id="roomcount"><?= $lang["mloading"] ?? 'Lädt...' ?></div>
				</div>
			</div>
		</div>
		
		<!-- Friendship Status -->
		<div class="box fade-in">
			<div class="title blue">
				<i class="fas fa-heart"></i> Freundschaftsstatus
			</div>
			<div class="mainBox">
				<?= friendList() ?>
			</div>
		</div>
		
		<!-- Twitter -->
		<?php if($config['twitterEnable'] == true): ?>
		<div class="box fade-in">
			<div class="title" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white;">
				<i class="fab fa-twitter"></i> <?= $lang["Mtwitter"] ?? 'Twitter' ?>
			</div>
			<div class="mainBox">
				<a class="twitter-timeline" 
				   data-width="100%" 
				   data-height="420" 
				   data-theme="light" 
				   href="<?= htmlspecialchars($config['twitter']) ?>">
					Tweets by <?= htmlspecialchars($config['hotelName']) ?>
				</a>
				<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
			</div>
		</div>
		<?php endif; ?>
		</div>
	</div>
	
	<!-- Footer -->
	<?php include_once 'includes/footer.php'; ?>
</div>
</body>
</html>
