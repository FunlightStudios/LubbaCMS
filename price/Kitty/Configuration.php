<?php
	/**
	 * @author Candan Tombas
	 */

	$phpPath = $_SERVER['DOCUMENT_ROOT'] . '/price';
	$kittyPath = $phpPath . '/Kitty';

	return (object) [
		# Production or Development
		'mode' => 'Production',
		
		# Save path for temps (null to use the default path from php.ini)
		'session_save_path' => '',

		# Should PHP automatically detect the country of the user and change the language?
		#
		# If you enable this function, make sure that you have geoIP installed.
		# http://php.net/manual/de/geoip.setup.php
		'country_auto_detect' => true,

		# Prefix for sessions
		'session_prefix' => 'pl_',

		# Application paths
		'paths' => (object) [
			'libraries' 	=> $kittyPath . '/Libraries',
			'framework' 	=> $kittyPath,
			'languages' 	=> $phpPath . '/Application/Languages',
			'templates' 	=> $phpPath . '/Application/Templates',
			'controllers' 	=> $phpPath . '/Application/Controllers',
			'models' 		=> $phpPath . '/Application/Models'
		],

		# Database connection
		'database' => (object) [
			'hostname'	=> '127.0.0.1',
			'username'	=> 'root',
			'password'	=> '',
			'database'	=> 'pricelist_2',
			'prefix'	=> '',
			'string'	=> 'mysql:host=%hostname%;dbname=%database%;charset=utf8'
		]
	];
