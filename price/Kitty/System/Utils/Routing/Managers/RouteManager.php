<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing\Managers {
	    use Kitty\System\Utils\Routing\Collectors\RouteCollector;

	    final class RouteManager {
	        private $routeCollector;

			/**
			 * @param RouteCollector $routeCollector
			 */
	        public function __construct(RouteCollector $routeCollector) {
	            $this->routeCollector = $routeCollector;
	        }

			/**
			 * @return RouteCollector
			 */
	        public function getCollector(): RouteCollector {
	            return $this->routeCollector;
	        }
	    }
	}
