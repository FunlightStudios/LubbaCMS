<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing\Interfaces {
	    use Kitty\System\Utils\Routing\Route;

	    interface IRoute {
	        public function onCall(Route $route, array $vars);
	        public function getRoutes();
	    }
	}
