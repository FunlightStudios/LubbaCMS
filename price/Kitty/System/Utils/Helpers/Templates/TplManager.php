<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Helpers\Templates {
		use Kitty\System\Utils\Helpers\Templates\Template;
		use Kitty\System\Configurations\Paths;

	    final class TplManager {
			private $paths;

			private $files = [];
			private $default;

			/**
			 * @param Paths 	$paths
 			 * @param string 	$default
			 */
			public function __construct(Paths $paths, string $default) {
				$this->paths = $paths;
				$this->default = new Template($paths, $default);

				$files = scandir($this->paths->getTemplatesPath());
				foreach($files as $key => $value) {
					if(is_dir($this->paths->getTemplatesPath() . '/' . $value) && $value != '.' && $value != '..') {
						$this->add($value, new Template($paths, $value));
					}
				}
			}

			/**
			 * @param string 	$dirname
			 * @param Template 	$template
			 */
	        public function add(string $dirname, Template $template) {
	            $this->files[$dirname] = $template;
	        }

			/**
			 * @param 	string $dirname
			 * @return 	Template
			 */
	        public function getTemplate(string $dirname): Template {
	            if($this->contains($dirname)) {
					return $this->files[$dirname];
				}

				return $this->default;
	        }

			/**
			 * @return boole
			 */
			public function contains(string $dirname): bool {
				if(array_key_exists($dirname, $this->files)) {
					return true;
				}

				return false;
			}

			/**
			 * @return boole
			 */
			public function exists(string $dirname): bool {
				if($this->contains($dirname)) {
					if($this->files[$dirname]->exists()) {
						return true;
					}
				}

				return false;
			}
	    }
	}
