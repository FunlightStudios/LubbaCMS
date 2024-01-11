<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Variables {
	    final class NullTemplateVariable extends NullVariable {
	        private $var;
	        private $file;

			/**
			 * @param string $var
			 * @param string $file
			 */
	        public function __construct(string $var, string $file) {
	            $this->var = $var;
	            $this->file = $file;
	        }

			/**
			 * @return string
			 */
	        public function __toString(): string {
	            return '{' . $this->file . '->$' . $this->var . '}';
	        }
	    }
	}
