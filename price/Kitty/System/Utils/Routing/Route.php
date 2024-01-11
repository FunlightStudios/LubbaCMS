<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing {
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;

	    final class Route {
	        private $url;
	        private $controller;
	        private $data;

			/**
			 * @param string	$url
			 * @param IRoute 	$controller
			 * @param string 	$data
			 */
	        public function __construct(string $url, IRoute $controller, string $data = '') {
	            $this->url = $url;
	            $this->controller = $controller;
	            $this->data = $data;
	        }

			/**
			 * @return string
			 */
	        public function getURL(): string {
	            return $this->url;
	        }

			/**
			 * @return IRoute
			 */
	        public function getController(): IRoute {
	            return $this->controller;
	        }

			/**
			 * @return string
			 */
	        public function getData(): string {
	            return $this->data;
	        }
	    }
	}
