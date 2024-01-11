<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing {
	    final class RouterResult {
	        private $routerInfo;

			/**
			 * @param RouterInfo $info
			 */
	        public function __construct(RouterInfo $info) {
	            $this->routerInfo = $info;
	        }

			/**
			 * @return Route
			 */
	        public function getRoute(): Route {
	            return $this->routerInfo->getRoute();
	        }

			/**
			 * @return int
			 */
	        public function getState(): int {
	            return $this->routerInfo->getState();
	        }

			/**
			 * @return mixed
			 */
	        public function callController() {
	            return $this->routerInfo->getRoute()->getController()->onCall($this->routerInfo->getRoute(), $this->routerInfo->getParameters());
	        }
	    }
	}
