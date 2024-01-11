<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates {

	    use Kitty\System\Patterns\MVC\Templates\Managers\TemplateManager;
	    use Kitty\System\Patterns\MVC\Templates\Variables\TemplateVariableString;
	    use Kitty\System\Patterns\MVC\Templates\Variables\NullTemplateVariable;
	    use Kitty\System\Patterns\MVC\Templates\Variables\TemplateVariableCallable;
	    use Kitty\System\Patterns\MVC\Templates\Variables\NullTemplateVariableCallback;

		use \Smarty as Smarty;

	    final class Template {
	        private $file;
	        private $path;
	        private $vars = [];
	        private $templateManager;

			private $smarty = null;
	        private $view = null;

			/**
			 * @param string         	$path
			 * @param string         	$file
			 * @param TemplateManager 	$templateManager
			 */
	        public function __construct(string $path, string $file, Smarty $smarty, TemplateManager $templateManager) {
	            $this->file = $file;
	            $this->path = $path;
				$this->smarty = $smarty;
	            $this->templateManager = $templateManager;
	        }

			/**
			 * @return Smarty
			 */
			public function getSmarty(): Smarty {
				return $this->smarty;
			}

			/**
			 * @return string
			 */
	        public function getFullPath(): string {
	            return $this->file . '/' . $this->path;
	        }

			/**
			 * @return TemplateManager
			 */
	        public function getTemplateManager(): TemplateManager {
	            return $this->templateManager;
	        }

			/**
			 * @param View $view
			 */
	        public function setView(View $view) {
	            $this->view = $view;
	        }

			/**
			 * @return View
			 */
	        public function getView(): View {
	            return $this->view;
	        }

			/**
			 * @param string 	$var
			 * @param mixed 	$val
			 */
	        public function __set(string $var, $val) {
	            if(is_callable($val)) {
	                $this->vars[$var] = new TemplateVariableCallable($var, $val);
	            } else {
	                $this->vars[$var] = new TemplateVariableString($var, $val);
	            }

				$this->smarty->assign($var, $this->vars[$var]);
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

	            return new NullTemplateVariable($var, $this->file);
	        }

			/**
			 * @param  string $var
			 * @param  array $args
			 * @return mixed
			 */
	        public function __call(string $var, array $args) {
	            $variable = $this->$var;

	            if($variable instanceof NullTemplateVariable) {
	                return new NullTemplateVariableCallback($var, $this->file, $args);
	            } else if($variable instanceof TemplateVariableCallable) {
	                return $variable->call($args);
	            }
	        }

	        public function display() {
				$this->smarty->display($this->path . '/' . $this->file);
	        }
	    }
	}
