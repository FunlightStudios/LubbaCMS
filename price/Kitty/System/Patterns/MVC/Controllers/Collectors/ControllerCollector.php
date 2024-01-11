<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Controllers\Collectors {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;

	    final class ControllerCollector {
	        private $controllers = [];

			/**
			 * @param Controller $controller
			 */
	        public function add($controller) {
	            $this->controllers[] = $controller;
	        }

			/**
			 * @return array
			 */
	        public function getAll(): array {
	            return $this->controllers;
	        }

			/**
			 * @param	mixed $instance
			 * @return	array
			 */
	        public function getByInstance($instance):array {
	            $result = [];

	            foreach($this->controllers as $controller) {
	                if($controller instanceof $instance) {
	                    $result[] = $controller;
	                }
	            }

	            return $result;
	        }
	    }
	}
