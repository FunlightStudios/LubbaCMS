<?php
	/**
	 * KITTY based on pKit (https://github.com/iExit1337/pKit)
	 * @author 	Candan Tombas
	 * @version	1.2
	 */

	$configuration = require_once __DIR__ . '/Configuration.php';

	@set_time_limit(0);
	@ini_set('memory_limit', -1);

	$mode = 'Production';
	if(ucfirst($configuration->mode) == 'Development') {
		$mode = 'Development';
	}

	if($configuration->session_save_path != null) {
		ini_set('session.save_path', $configuration->session_save_path);
	}

   	if(session_status() === PHP_SESSION_NONE) session_start();

	require_once __DIR__ . '/Modes/' . $mode . '.php';
	require_once __DIR__ . '/System/Setup.php';
