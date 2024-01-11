<?php
	
	if(isset($_GET['userref'])) {
		$userref = $_GET['userref'];
	}
	else{
		$userref = null;
	}
	
?>
<head>
<link rel="stylesheet" href="/templates/weed/materialize/css/materialize.css" type="text/css">
<link rel="stylesheet" href="/templates/weed/css/style.css" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="//code.jquery.com/jquery-2.2.2.js"></script>
<script src="https://use.fontawesome.com/8e2a5cc178.js"></script>
<script src="./templates/brain/js/register.js?v=<?= rand(1,100011111) ?>"></script>
<link href="/templates/brain/style/css/avatargenerate.css" rel="stylesheet" />
<meta name="description" content="Virtuele wereld waar je vrienden kunt maken en ontmoeten.">
<meta name="keywords" content="<?= $config['hotelName'] ?>, <?= $config['hotelName'] ?>hotel, <?= $config['hotelName'] ?> hotel, virtueel, wereld, sociaal netwerk, gratis, community, avatar, chat, online, tiener, roleplaying, doe mee, sociaal, groepen, forums, veilig, speel, games, on, vrienden, tieners, zeldzaams, zeldzame meubi, verzamelen, maak, verzamel, kom in contact, meubi, meubeks, huisdieren, kamer inrichten, delen, uitdrukking, badges, hangout, muziek, beroemdheid, VIP-visits, celebs, mmo, mmorpgs, massive multiplayer">
<title><?= $config['hotelName'] ?> Hotel: find Freunde, hab Spaß... !</title>	
</head>
<body style="background: linear-gradient(#4ea0f3, #ffffff);    background-repeat: no-repeat; background-attachment: fixed; height: 0%;">
<div class="cloud x1"></div><div class="cloud x2"></div><div class="cloud x3"></div><div class="cloud x4"></div><div class="cloud x5"></div>
<div class="container" style="width: 860px;">
<div class="hotelheader"></div>
<a href="/" class="logo"></a>

<div style="height: 20px; background: #fff;box-shadow: 0px 1px 5px 0.1px #5e5858; padding: 15px; line-height: 0px;">Willkommen bei <?= $config['hotelName'] ?> Hotel, wir wünschen dir eine angenehme Zeit im Hotel!</div>

<div style="float:left; width: 41%; margin-top: 20px;">
<div class="panel-red">&nbsp;&nbsp;&nbsp;Registrieren im <?= $config['hotelName'] ?> Hotel! <i style="float: right; margin-top: 15px; margin-right: 15px;" class="fa fa-id-card-o" aria-hidden="true"></i></div>
<div  style="overflow: hidden;" class="panel-body">
<div class="input-field">
<input type="text" name="username" id="username"  class="validate" autocomplete="off" style=" margin-top: 10px; margin-left: 10px;margin-bottom: 3px; width: 300px;">
<label style="margin-top: 10px; font-weight: 300; font-size: 15px;margin-left: 10px;" for="last_name">Username</label>
</div>

<div class="input-field">
<input type="text" name="motto" id="motto"  class="validate" autocomplete="off" style=" margin-top: 10px; margin-left: 10px;margin-bottom: 3px; width: 300px;">
<label style="margin-top: 10px; font-weight: 300; font-size: 15px;margin-left: 10px;" for="last_name">Motto</label>
</div>

<div class="input-field">
<input type="text" name="email" id="email"  class="validate" autocomplete="off" style=" margin-top: 10px; margin-left: 10px;margin-bottom: 3px; width: 300px;">
<label style="margin-top: 10px; font-weight: 300; font-size: 15px;margin-left: 10px;" for="last_name">Email</label>
</div>

<div class="input-field">
<input type="password" name="password" id="password"  class="validate" autocomplete="off" style=" margin-top: 10px; margin-left: 10px;margin-bottom: 3px; width: 300px;">
<label style="margin-top: 10px; font-weight: 300; font-size: 15px;margin-left: 10px;" for="last_name">Passwort</label>
</div> 

<div class="input-field">
<input type="password" name="password_repeat" id="password_repeat"  class="validate" autocomplete="off" style=" margin-top: 10px; margin-left: 10px;margin-bottom: 3px; width: 300px;">
<label style="margin-top: 10px; font-weight: 300; font-size: 15px;margin-left: 10px;" for="last_name">Passwort wiederholen</label>
</div>

<button type="submit" name="register" id="registerSubmit" class="btn"><?php echo $lang['Rregister']; ?></button> 
</div> 
</div> 

<div class="form-group">
								<div style="float: right; margin-top: 20px;" class="col-lg-8">
									<div id="avatarSelector" class="builder-viewport" style="margin-top: -45px;">
										<div class="main-navigation">
											<ul>
												<li class="active">
													<a href="#" data-navigate="hd" data-subnav="gender"><img src="/templates/brain/style/images/avatarreg/body.png" /></a>
												</li>
												<li>
													<a href="#" data-navigate="hr" data-subnav="hair"><img src="/templates/brain/style/images/avatarreg/hair.png" /></a>
												</li>
												<li>
													<a href="#" data-navigate="ch" data-subnav="tops"><img src="/templates/brain/style/images/avatarreg/tops.png" /></a>
												</li>
												<li>
													<a href="#" data-navigate="lg" data-subnav="bottoms"><img src="/templates/brain/style/images/avatarreg/bottoms.png" /></a>
												</li>
											</ul>
										</div>
										<div class="sub-navigation">
											<ul id="gender" class="display">
												<li>
													<a href="#" class="male nav-selected" data-gender="M"></a>
												</li>
												<li>
													<a href="#" class="female" data-gender="F"></a>
												</li>
											</ul>
											<ul id="hair" class="hidden">
												<li>
													<a href="#" class="hair nav-selected" data-navigate="hr"></a>
												</li>
												<li>
													<a href="#" class="hats" data-navigate="ha"></a>
												</li>
												<li>
													<a href="#" class="hair-accessories" data-navigate="he"></a>
												</li>
												<li>
													<a href="#" class="glasses" data-navigate="ea"></a>
												</li>
												<li>
													<a href="#" class="moustaches" data-navigate="fa"></a>
												</li>
											</ul>
											<ul id="tops" class="hidden">
												<li>
													<a href="#" class="tops nav-selected" data-navigate="ch"></a>
												</li>
												<li>
													<a href="#" class="chest" data-navigate="cp"></a>
												</li>
												<li>
													<a href="#" class="jackets" data-navigate="cc"></a>
												</li>
												<li>
													<a href="#" class="accessories" data-navigate="ca"></a>
												</li>
											</ul>
											<ul id="bottoms" class="hidden">
												<li>
													<a href="#" class="bottoms nav-selected" data-navigate="lg"></a>
												</li>
												<li>
													<a href="#" class="shoes" data-navigate="sh"></a>
												</li>
												<li>
													<a href="#" class="belts" data-navigate="wa"></a>
												</li>
											</ul>
										</div>
										<div id="clothes-colors">
											<div id="clothes"></div>
											<div id="colors"></div>
										</div>
										<div id="avatar">
											<img id="myHabbo" value="" src="" alt="My Habbo" title="My Habbo" />
											<input type="hidden" name="habbo-avatar" id="avatar_code">
										</div>
									</div>
								</div>
							</div>






<?php include_once 'includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-latest.min.js?v=4" type="text/javascript"></script>
		<script src="/templates/brain/style/js/jquery.avatargenerate.js?v=17" type="text/javascript"></script>	