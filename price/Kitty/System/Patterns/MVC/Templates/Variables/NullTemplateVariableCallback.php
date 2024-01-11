<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Variables {
	    final class NullTemplateVariableCallback extends NullVariable {
	        private $var;
	        private $file;
	        private $args;

			/**
			 * @param string 	$var
			 * @param string 	$file
			 * @param array 	$args
			 */
	        public function __construct(string $var, string $file, array $args) {
	            $this->var = $var;
	            $this->file = $file;
	            $this->args = $args;
	        }

			/**
			 * @return string
			 */
	        public function __toString(): string {
	            return '{' . $this->file . '->' . $this->var . '(' . implode(',', $this->args) . ')}';
	        }
	    }
	}
