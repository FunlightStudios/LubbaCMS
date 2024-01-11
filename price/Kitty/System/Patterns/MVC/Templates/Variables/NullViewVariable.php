<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Variables {
	    final class NullViewVariable extends NullVariable {
	        private $var;

			/**
			 * @param string $var
			 */
	        public function __construct(string $var) {
	            $this->var = $var;
	        }

			/**
			 * @return string
			 */
	        public function __toString(): string {
	            return '{$this->getView()->' . $this->var . '}';
	        }
	    }
	}
