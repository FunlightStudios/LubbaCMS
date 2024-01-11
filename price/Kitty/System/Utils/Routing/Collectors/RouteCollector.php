<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing\Collectors {
	    use Kitty\System\Exceptions\pKitException;
	    use Kitty\System\Utils\Routing\Route;

	    final class RouteCollector {
	        private $routes = [];

	        /**
	         * @param Route $route
	         */
	        public function add(Route $route) {
	            if(!$this->exists($route)) {
	                $this->routes[] = $route;
	            } else {
	                throw new KittyException(__DIR__ . '\RouteCollector.php', 16, 'Cannot add an already existing route', $route);
	            }
	        }

	        /**
	         * @param 	Route $route
	         * @return	boole
	         */
	        private function exists(Route $route): bool {
	            foreach($this->routes as $_route) {
	                if($route === $_route) {
	                    return true;
	                } else if($route->getURL() == $_route->getURL()) {
	                    return true;
	                }
	            }

	            return false;
	        }

	        /**
	         * @return array
	         */
	        public function getRoutes(): array {
	            return $this->routes;
	        }
	    }
	}
