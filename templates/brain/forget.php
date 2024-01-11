<style>
	.error {
    text-align: center;
    font-size: 13px;
    background: #f44336;
    display: none;
    width: 100%;
    color: #fff;
    padding: 0 10px;
    border-radius: 2px;
    margin-bottom: 8px;
    line-height: 40px;
	}
</style>
<?php
	
	if(isset($_GET['key'])) {
		$key = $_GET['key'];
	}
	else{
		$key = '';
	}
	
?>
<html lang="nl-NL">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title><?= $config['hotelName'] ?> | Requesting password reset</title>
		<link href="/templates/brain/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
		<link href="https://bootswatch.com/paper/bootstrap.css" rel="stylesheet" type="text/css">
		<link href="/templates/brain/style/css/register.css" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300normal,300italic,400normal,400italic,600normal,600italic,700normal,700italic,800normal,800italic&amp;subset=all" rel="stylesheet" type="text/css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
		<link href="/templates/brain/style/css/avatargenerate.css" rel="stylesheet" />
		<meta name="description" content="Virtuele wereld waar je vrienden kunt maken en ontmoeten.">
		<meta name="keywords" content="<?= $config['hotelName'] ?>, <?= $config['hotelName'] ?>hotel, <?= $config['hotelName'] ?> hotel, virtueel, wereld, sociaal netwerk, gratis, community, avatar, chat, online, tiener, roleplaying, doe mee, sociaal, groepen, forums, veilig, speel, games, on, vrienden, tieners, zeldzaams, zeldzame meubi, verzamelen, maak, verzamel, kom in contact, meubi, meubeks, huisdieren, kamer inrichten, delen, uitdrukking, badges, hangout, muziek, beroemdheid, VIP-visits, celebs, mmo, mmorpgs, massive multiplayer">
	</head>
	<body>
		<nav style="height: 56px;" class="navbar navbar-default">
			<div class="navbar-header"> <a href="/"></a>    
			</div>
			<div class="container"><div class="users-online" id="users-online"><span id="usersOnline"><?= Game::usersOnline() ?></span> <?= $config['hotelName'] ?>'s online.</div>
			</nav>
			<div class="container">
			<div class="logotipo"><a href="/"> <img src="/templates/brain/style/images/logo/logo.png" style="margin-top: -20px"> </a></div>
				<div style="clear:both;"></div>
				<div class="panel panel-success" style="    width: 100%;float: left;padding: 8px;">
					<form method="post" class="form-horizontal">
						<fieldset>
							<legend><?= $lang["forgetPwTitle"] ?></legend>
							<?php sendEmailNewPassword(); ?>
							<div class="form-group">
								<label style= "width: 20%;" class="col-lg-4 control-label"><?= $lang["forgetEmail"] ?></label>
								<div class="col-lg-8">
									<input style="width:400px"value= "<?php echo $key; ?>"type="email" class="form-control" name="useremail" id="useremail" placeholder="<?= $lang["forgetEmail"] ?>">
									<i class="glyphicon glyphicon-envelope form-control-feedback" style="right: 10px;"></i>
								</div>
							</div>
							
							<div class="form-group" style="text-align: center;">
								<div class="col-lg-8 col-lg-offset-2">
								</div>
								</div
							<div class="form-group" style="text-align: right;">
							<div class="col-lg-8 col-lg-offset-4"><br>
								<a href="/index" class="btn btn-danger"><?php echo $lang['Rback']; ?></a>
								<button href="/me"   type="submit" name="sendresetpasswordnow"class="btn btn-primary"><?= $lang["forgetSendButton"] ?></button>
							</div>
						</div>
					</fieldset>
				</form>
				
				<div style="float: right; width: 42%; margin-top: -200px" class="list-group">
				
					<div class="list-group-item">
						<div class="subimage1"></div>
						<h4 class="list-group-item-heading"><?php echo $lang['forgetTextHeader']; ?></h4>
						<p class="list-group-item-text"><?php echo $lang['forgetTextInfo']; ?></p>
					</div>
					
				</div>
				<div style="clear:both; margin-top:-30px;"></div><footer class="footer" style="font-size: 13.5px; font-weight: 100; color:white;">
					<center>
					<p>	<?php echo $lang['RCopyright']; ?></p>
					</div>
				</body>
			</html>
			<script src="https://code.jquery.com/jquery-latest.min.js?v=4" type="text/javascript"></script>
		<script src="/templates/brain/style/js/jquery.avatargenerate.js?v=13" type="text/javascript"></script>																