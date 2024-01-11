<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Helpers\Multilingual {
		use Kitty\System\Configurations\Paths;

	    final class Language {
			private $code;
			private $paths;
			private $file;

			/**
			 * @param Paths  $paths
			 * @param string $file
			 */
			public function __construct(Paths $paths, string $code, string $file) {
				$this->paths = $paths;
				$this->code = $code;
				$this->file = $file;
			}

			/**
			 * @return string
			 */
			public function getFile(): string {
				return $this->file;
			}

			/**
			 * @return string
			 */
			public function getCode(): string {
				return $this->code;
			}

			/**
			 * @return bool
			 */
			public function exists(): bool {
				if(file_exists($this->paths->getLanguagesPath())) {
					return true;
				}

				return false;
			}

			/**
			 * @return array
			 */
			public function json(): array {
				if($this->exists()) {
					$content = file_get_contents($this->paths->getLanguagesPath() . '/' . $this->getFile());
					return json_decode($content, true);
				}

				return [];
			}
	    }
	}
