{assign var=language value=$this->getLanguage()->json()}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
		<!-- Title -->
		<title>{if isset($pageName)}{$settings->getSiteName()} - {$pageName}{else}{$settings->getSiteName()}{/if}</title>

		<!-- Meta Tags -->
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />

		<meta name="language" content="{$this->getLanguage()->getCode()|lower}" />
		<meta name="audience" content="all" />
		<meta name="cache-control" content="no-cache" />
		<meta name="robots" content="index, robots" />
		<meta name="pragma" content="no-cache" />

		<!-- Favicon -->
		<meta name="mobile-web-app-capable" content="yes" />
		<link rel="icon" sizes="192x192" href="{$settings->getWebPath()}/android-desktop.png" />

		<meta name="apple-mobile-web-app-capable" content="yes" />
	    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
	    <meta name="apple-mobile-web-app-title" content="{$settings->getSiteName()}" />
	    <link rel="apple-touch-icon-precomposed" href="{$settings->getWebPath()}/ios-desktop.png" />

		<link rel="shortcut icon" href="{$settings->getWebPath()}/favicon.png" />

		<!-- Fonts -->
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" />

		<!-- CSS Stylesheets -->
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
		<link rel="stylesheet" type="text/css" href="{$settings->getWebPath()}/css/libs/material.min.css" />
		<link rel="stylesheet" type="text/css" href="{$settings->getWebPath()}/css/style.css" />

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- BEGIN Scripts -->
		<script type="text/javascript" src="{$settings->getWebPath()}/scripts/libs/jquery.js"></script>
		<script type="text/javascript" src="{$settings->getWebPath()}/scripts/libs/jquery.waypoints.js"></script>
		<script type="text/javascript" src="{$settings->getWebPath()}/scripts/libs/material.js"></script>
		<script type="text/javascript" src="{$settings->getWebPath()}/scripts/main.js"></script>
		<script type="text/javascript">
			function getSitePath() {
				return '{$settings->getSitePath()}';
			}
		</script>
	</head>
	<body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base">
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
			<header class="mdl-layout__header mdl-layout__header--scroll mdl-color--primary">
				<div class="mdl-layout--large-screen-only mdl-layout__header-row">
				</div>
				<div class="mdl-layout--large-screen-only mdl-layout__header-row">
					<h3>{$settings->getSiteName()}</h3>
				</div>
				<div class="mdl-layout--large-screen-only mdl-layout__header-row">
				</div>
				<div class="mdl-layout__tab-bar mdl-js-ripple-effect mdl-color--navigation">
					<a href="{$settings->getSitePath()}" class="mdl-layout__tab {if $pageId == 'indexView'}is-active{/if}">{$language['Titles']['Homepage']}</a>
					{if $this->getUser() != null}
					<a href="{$settings->getSitePath()}/account" class="mdl-layout__tab {if $pageId == 'accountView'}is-active{/if}">{$language['Titles']['Account']}</a>
					<a href="{$settings->getSitePath()}/logout" class="mdl-layout__tab">{$language['Titles']['Logout']}</a>
					{else}
					<a href="{$settings->getSitePath()}/login" class="mdl-layout__tab {if $pageId == 'loginView'}is-active{/if}">{$language['Titles']['Login']}</a>
					<a href="{$settings->getSitePath()}/register" class="mdl-layout__tab {if $pageId == 'registerView'}is-active{/if}">{$language['Titles']['Register']}</a>
					{/if}
				</div>
			</header>
