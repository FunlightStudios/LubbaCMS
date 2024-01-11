<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Templates\Managers {
	    use Kitty\System\Patterns\MVC\Templates\Template;

		use \Smarty as Smarty;

	    final class TemplateManager {
	        private $cachedTemplates = [];
	        private $path;
			private $smarty = null;

			/**
			 * @param Smarty $smarty
			 * @param string $path
			 */
	        public function __construct(string $path, Smarty $smarty) {
	            $this->path = $path;
				$this->smarty = $smarty;
	        }

			/**
			 * @param  string $tpl
			 * @return Template
			 */
	        private function create(string $tpl): Template {
	            $tpl = new Template($this->path, $tpl, $this->smarty, $this);
	            $this->cachedTemplates[] = $tpl;

	            return $tpl;
	        }

			/**
			 * @param  string $tpl
			 * @return Template
			 */
	        private function get(string $tpl) {
	            foreach($this->cachedTemplates as $template) {
	                if($template->getFile() == $tpl) {
	                    return $template;
	                }
	            }

	            return null;
	        }

			/**
			 * @param  string $tpl
			 * @return Template
			 */
	        private function exists(string $tpl) {
	            return $this->get($tpl) != null;
	        }

			/**
			 * @param  string $file
			 * @return Template
			 */
	        public function make(string $file): Template {
	            if(!$this->exists($file)) {
	                $tpl = $this->create($file);
	            } else {
	                $tpl = $this->get($file);
	            }

	            return $tpl;
	        }
	    }
	}
