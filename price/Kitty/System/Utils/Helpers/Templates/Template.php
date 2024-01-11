<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Helpers\Templates {
		use Kitty\System\Configurations\Paths;

	    final class Template {
			private $paths;

			private $name;
			private $dirname;

			/**
			 * @param Paths  $paths
			 * @param string $name
			 * @param string $dirname
			 */
			public function __construct(Paths $paths, string $dirname) {
				$this->paths = $paths;

				//$this->name = $name;
				$this->dirname = $dirname;
			}

			/**
			 * @return string
			 */
			public function getName(): string {
				return $this->name;
			}

			/**
			 * @return string
			 */
			public function getDirectoryName(): string {
				return $this->dirname;
			}

			/**
			 * @return boole
			 */
			public function exists(): bool {
				if(is_dir($this->paths->getTemplatesPath() . $this->getDirectoryName())) {
					return true;
				}

				return false;
			}
	    }
	}
