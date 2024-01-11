<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing {
	    final class RouterInfo {
	        private $route;
	        private $state;

	        private $parameters = [];

			/**
			 * @param int $state
			 */
	        public function setState(int $state) {
	            $this->state = $state;
	        }

			/**
			 * @param Route $route
			 */
	        public function setRoute(Route $route) {
	            $this->route = $route;
	        }

			/**
			 * @param string $key
			 * @param string $val
			 */
	        public function setParameter(string $key, $val) {
	            $this->parameters[$key] = $val;
	        }

			/**
			 * @return array
			 */
	        public function getParameters(): array {
	            return $this->parameters;
	        }

			/**
			 * @return Route
			 */
	        public function getRoute(): Route {
	            return $this->route;
	        }

			/**
			 * @return int
			 */
	        public function getState(): int {
	            return $this->state;
	        }
	    }
	}
