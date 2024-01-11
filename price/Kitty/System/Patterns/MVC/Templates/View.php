<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates {
	    use Kitty\System\Patterns\MVC\Templates\Variables\TemplateVariableString;
	    use Kitty\System\Patterns\MVC\Templates\Variables\TemplateVariableCallable;
	    use Kitty\System\Patterns\MVC\Templates\Variables\NullViewVariable;
	    use Kitty\System\Patterns\MVC\Templates\Variables\NullViewVariableCallback;

	    final class View {
	        private $vars = [];

			/**
			 * @param Template $tpl
			 */
	        public function display(Template $tpl) {
	            $tpl->setView($this);
	            $tpl->display();
	        }

			/**
			 * @param string $var
			 * @param mixed $val
			 */
	        public function __set(string $var, $val) {
	            if(is_callable($val)) {
	                $this->vars[$var] = new TemplateVariableCallable($var, $val);
	            } else {
	                $this->vars[$variant_abs] = new TemplateVariableString($var, $val);
	            }
	        }

			/**
			 * @param  string $var
			 * @return mixed
			 */
	        public function __get(string $var) {
	            foreach($this->vars as $_var) {
	                if($_var->getVariable() == $var) {
	                    return $_var;
	                }
	            }

	            return new NullViewVariable($var);
	        }

			/**
			 * @param 	string $var
			 * @param  	array $args
			 * @return 	mixed
			 */
	        public function __call(string $var, array $args) {
	            $variable = $this->$var;

	            if($variable instanceof NullViewVariable) {
	                return new NullViewVariableCallback($var, $args);
	            } else if($variable instanceof TemplateVariableCallable) {
	                return $variable->call($args);
	            }
	        }
	    }
	}
