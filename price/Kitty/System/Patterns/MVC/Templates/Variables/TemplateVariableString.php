<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Variables {
	    final class TemplateVariableString {
	        private $variable;
	        private $value;

			/**
			 * @param string $var
			 * @param string $val
			 */
	        public function __construct(string $var, $val) {
	            $this->variable = $var;
	            $this->value = $val;
	        }

			/**
			 * @return string
			 */
	        public function __toString(): string {
	            return (string) $this->value;
	        }

			/**
			 * @param 	string 	$name
			 * @param 	array 	$arguments
			 * @return 	mixed
			 */
	   		public function __call(string $name, array $arguments) {
		   		return call_user_func_array([$this->getValue(), $name], $arguments);
	   		}

			/**
			 * @return string
			 */
	        public function filter(): string {
	            return htmlentities($this->value, ENT_COMPAT, 'utf-8');
	        }

			/**
			 * @return string
			 */
	        public function getVariable(): string {
	            return $this->variable;
	        }

			/**
			 * @return mixed
			 */
	        public function getValue() {
	            return $this->value;
	        }

			/**
			 * @param 	string $char
			 * @return 	array
			 */
	        public function split(string $char): array {
	            return explode($char, $this->value);
	        }

			/**
			 * @return int
			 */
	        public function length(): int {
	            return strlen($this->value);
	        }

			/**
			 * @return string
			 */
	        public function toUpper(): string {
	            return strtoupper($this->value);
	        }

			/**
			 * @return string
			 */
	        public function toLower(): string {
	            return strtolower($this->value);
	        }

			/**
			 * @return bool
			 */
			public function isNull(): bool {
				return false;
			}

			/**
			 * @return bool
			 */
	        public function contains(string $str): bool {
	            return substr($str, $this->value) !== 0;
	        }
	    }
	}
