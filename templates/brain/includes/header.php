<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Ubuntu:regular,bold&subset=Latin">
		<link rel="stylesheet" href="/templates/brain/style/css/main2.css?v=22" type="text/css">
		<link rel="stylesheet" href="/templates/brain/style/css/home.css" type="text/css">
		<link rel="stylesheet" href="/templates/brain/bootstra/css/bootstrap.css" type="text/css">
		<!--<link rel="stylesheet" href="/templates/brain/custom/css/style.css" type="text/css">-->
		<!--<link rel="stylesheet" href="/templates/brain/custom/css/script.js" type="text/js">-->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
	</head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/jquery-latest.js"></script>
	<script type="text/javascript">
        $(document).ready(function(e) {
            $.ajaxSetup({
                cache:true
			});
            setInterval(function() {
                $('#onlinecount').load('/onlinecount');
			}, 1500);
            $( "#onlinecount").click(function() {
				$('#onlinecount').load('/onlinecount');
			});
		});
	</script>
	<script type="text/javascript">
        $(document).ready(function(e) {
            $.ajaxSetup({
                cache:true
			});
            setInterval(function() {
                $('#roomcount').load('/roomcount');
			}, 5500);
            $( "#roomcount").click(function() {
				$('#roomcount').load('/roomcount');
			});
		});
		</script>
	<body>
		<header id="mainheader">
			<div class="center">
				<a href="#">
					<div class="head_enter">
						<?php
						if (User::userData('rank') > '3')
						{
							echo'
							<div class="btn btn-danger"><a href="/adminpan/dash">Management </div>
							';
						}

						if (User::userData('rank') > '8')
						{
							echo'
							<div class="btn btn-warning"><a href="/housekeeping/index.php">HK </div>
							';
						}
					?>
						<a href="/client" onclick="window.open('/client','new','toolbar=0,scrollbars=0,location=1,statusbar=1,menubar=0,resizable=1,width=1270,height=700');return false;" class="btn btn-success"><?= $lang["Hgoto"] ?> &#10149; </a>
						<!--<a onclick="<?= $config['hotelUrl'] ?>/logout" href="<?= $config['hotelUrl'] ?>/logout" class="btn btn-danger"><?= $lang["HsignOut"] ?></a>-->
					</div>
					<div class="wrap">
						<div class="logo">
						</div>
					</a>
					<div style="margin-left: 16px; margin-top: 118px" id="onlinecount"><small><b><?= Game::usersOnline() ?></b> Lubba`s online.</small></div>

				</div>

			<!-- Nav Avatar -->
			<div class="navavatarbox">
				<div class="navavatar">
					<div class="avatar" style="background-image:url(<?= $config['habboImagingUrl'] ?><?= User::userData('look') ?>&amp;direction=2&amp;head_direction=3&amp;action=sit,wav&amp;gesture=sml);"></div>
				</div>
			</div>


			</header>
			<nav>

				<div id="navigator">
					<div class="center">
						<ul>
							<li class="blauw">
								<a href="/"><?= User::userData('username') ?></a>
								<div class="submenu">
									<a href="/me"><?= User::userData('username') ?></a>
									<a href="/settingspassword"><?= $lang["Naccountsettings"] ?></a>
									<a href="/home/<?= User::userData('username') ?>"><?= $lang["Nmyprofile"] ?></a>
									<a href="/sessionlog"><?= $lang["Nsessionlog"] ?></a>
									<a href="/logout"><?= $lang["NsignOut"] ?></a>
								</div>
							</li>
							<li class="rood">
								<a href="/community"><?= $lang["Ncommunity"] ?></a>
								<div class="submenu">
									<a href="/community"><?= $lang["Ncommunity"] ?></a>

									<a href="<?php
										$getLastNewsId = $dbh->prepare("SELECT id FROM cms_news ORDER BY ID DESC LIMIT 1");
										$getLastNewsId->execute();
										$row = $getLastNewsId->fetch();
										echo "/news/".$row['id']."";
										?>">
									<?= $lang["Nnews"] ?></a>
									<a href="/advertentie_tips"><?= $lang["Nadvertisementtips"] ?></a>
									<a href="/stats"><?= $lang["Nstatistics"] ?></a>
									<a href="/online">Lubba`s Online</a>
								</div>
							</li>
							<li class="paars">
								<a href="/staff">Mitarbeiter</a>
								<div class="submenu">
									<a href="/staff">Mitarbeiter</a>
									<?php if($config['EnableStaffApply'] == true)
									 { ?>
									<a href="/sollicitaties"><?= $lang["Nstaffapply"] ?></a>
									<?php } ?>
									<a href="/teams"><?= $lang["Nteams"] ?></a>
								</div>
							</li>
							<li class="groen">
								<a href="/vip"><?= $lang["Sshopnaviname"] ?></a>
								<div class="submenu">
									<a href="/vip"><?= $lang["Vvipnaviname"] ?></a>
								</div>
							</li>
							<a href="<?= $config['hotelUrl'] ?>/logout"><li class="logout"><?= $lang["NsignOut"] ?></li></a>
						</ul>
					</div>
				</nav>
