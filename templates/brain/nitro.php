<?php
	staffCheck();
	Game::sso('client');	
	Game::homeRoom();	
?>
<html>
</body>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?= $config['hotelName'] ?> - Game</title>
	<script src="/templates/brain/client/js/jquery-latest.js" type="text/javascript"></script>
	<script src="/templates/brain/client/js/jquery-ui.js" type="text/javascript"></script>
	<script src="/templates/brain/client/js/flashclient.js"></script>
	<script src="/templates/brain/client/js/flash_detect_min.js"></script>
	<script src="/templates/brain/client/js/client.js" type="text/javascript"></script>
	<link rel="stylesheet" href="/templates/brain/client/css/client.css?v=5" type="text/css">
	</head>
<body>
	<?php
		if($hotel['onlineCounter'] == true)
		{
		?>
		<script type="text/javascript">
			$(document).ready(function(e) {
				$.ajaxSetup({
					cache:true
				});
				setInterval(function() {
					$('#onlinecount').load('/onlinecountclient');
				}, 8000);
				$( "#onlinecount").click(function() {
					$('#onlinecount').load('/onlinecountclient');
				});
			});
		</script>
		<div class="clientOnlineBox">
			<div class="clientOnlineBoxTxt" id="onlinecount"><small><b><?= Game::usersOnline() ?></b> <?= $config['hotelName'] ?>'s online</small></div>
			<img src="/templates/brain/client/images/onlineiconclient.png" style="width:29px;height:28px;ht; */position: absolute;margin-left: 130px;margin-top: -15px;">
		</div>
		<?php
		}
	?>
	<center>
	    <style>
            body{
                line-height:normal!important;
                background-color:#0E151C;
                margin:auto;
            }
            #client-div, #client-ui{
                display: block;       /* iframes are inline by default */
                background: #000;
                border: none;         /* Reset default border */
                height: 100vh;        /* Viewport-relative units */
                width: 100vw;
            }
        </style>
		     <iframe id="client-div" src="/nitro/index.html?sso=<?= User::userData('auth_ticket') ?>"></iframe>
</body>
</html>
</center>
</div>
</head>