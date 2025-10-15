<?php
	declare(strict_types=1);
	
	// Check if user is logged in
	if (!isset($_SESSION['id'])) {
		header('Location: /');
		exit;
	}
	
	// Generate SSO token
	Game::sso('client');	
	Game::homeRoom();
	
	// Get SSO token
	$ssoToken = User::userData('auth_ticket');
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?= htmlspecialchars($config['hotelName']) ?> - Nitro Client</title>
	<link rel="shortcut icon" href="<?= htmlspecialchars($config['favicon'] ?? '/favicon.ico') ?>">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			background: #0E151C;
			overflow: hidden;
			font-family: 'Ubuntu', sans-serif;
		}
		
		#nitro-container {
			width: 100vw;
			height: 100vh;
			position: relative;
		}
		
		#client-iframe {
			display: block;
			width: 100%;
			height: 100%;
			border: none;
			background: #000;
		}
		
		.loading-screen {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			z-index: 9999;
			transition: opacity 0.5s ease;
		}
		
		.loading-screen.hidden {
			opacity: 0;
			pointer-events: none;
		}
		
		.loading-logo {
			font-size: 48px;
			font-weight: 700;
			color: white;
			margin-bottom: 30px;
			text-shadow: 0 4px 20px rgba(0,0,0,0.3);
		}
		
		.loading-spinner {
			width: 60px;
			height: 60px;
			border: 4px solid rgba(255,255,255,0.3);
			border-top-color: white;
			border-radius: 50%;
			animation: spin 1s linear infinite;
		}
		
		.loading-text {
			margin-top: 20px;
			color: white;
			font-size: 16px;
			font-weight: 500;
		}
		
		@keyframes spin {
			to { transform: rotate(360deg); }
		}
	</style>
</head>
<body>
	<div id="nitro-container">
		<!-- Loading Screen -->
		<div class="loading-screen" id="loadingScreen">
			<div class="loading-logo"><?= htmlspecialchars($config['hotelName']) ?></div>
			<div class="loading-spinner"></div>
			<div class="loading-text">Nitro Client wird geladen...</div>
		</div>
		
		<!-- Nitro Client iFrame -->
		<iframe 
			id="client-iframe" 
			src="/nitro/index.html?sso=<?= htmlspecialchars($ssoToken) ?>"
			allow="camera; microphone; fullscreen"
			allowfullscreen>
		</iframe>
	</div>
	
	<script>
		// Hide loading screen when iframe loads
		const iframe = document.getElementById('client-iframe');
		const loadingScreen = document.getElementById('loadingScreen');
		
		iframe.addEventListener('load', function() {
			setTimeout(() => {
				loadingScreen.classList.add('hidden');
			}, 1000);
		});
		
		// Fallback: Hide after 5 seconds if load event doesn't fire
		setTimeout(() => {
			loadingScreen.classList.add('hidden');
		}, 5000);
		
		// Console info
		console.log('%c🎮 Lubba Hotel - Nitro Client', 'font-size: 20px; font-weight: bold; color: #667eea;');
		console.log('%cSSO Token: <?= htmlspecialchars($ssoToken) ?>', 'color: #22c55e;');
		console.log('%cWebSocket: ws://127.0.0.1:30000', 'color: #22c55e;');
		console.log('%cWenn der Client nicht lädt, stelle sicher dass der Emulator läuft!', 'color: #f59e0b;');
	</script>
</body>
</html>