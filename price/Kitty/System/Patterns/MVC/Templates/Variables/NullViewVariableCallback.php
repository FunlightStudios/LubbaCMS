<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Variables {
	    final class NullViewVariableCallback extends NullVariable {
	        private $var;
	        private $args;

			/**
			 * @param string 	$var
			 * @param array 	$args
			 */
	        public function __construct(string $var, array $args) {
	            $this->var = $var;
	            $this->args = $args;
	        }

			/**
			 * @return string
			 */
	        public function __toString(): string {
	            return '{$this->getView()->' . $this->var . '(' . implode(',', $this->args) . ')}';
	        }
	    }
	}
