<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Helpers\Multilingual {
		use Kitty\System\Utils\Helpers\Multilingual\Language;
		use Kitty\System\Configurations\Paths;

		use Kitty\System\Common;

	    final class Multilingual {
			private $paths;

			private $files = [];
			private $default;
			private $autoDetect;

			/**
			 * @param Paths 	$paths
 			 * @param string 	$default
			 */
			public function __construct(Paths $paths, string $default, bool $autoDetect = false) {
				$this->paths = $paths;

				$this->autoDetect = $autoDetect;
				$this->default = new Language($paths, $default, $default . '.json');

				$files = scandir($this->paths->getLanguagesPath());
				foreach($files as $key => $value) {
					if(strpos(strtolower($value), '.json') !== false) {
						$path = pathinfo($value);

						$this->add($path['filename'], new Language($paths, $path['filename'], $path['basename']));
					}
				}

				if($autoDetect) {
					$language = Common::getCountryCode();

					if($this->exists($language)) {
						$this->default = new Language($paths, $language, $language . '.json');
					}
				}
			}

			/**
			 * @param string 	$key
			 * @param Language 	$language
			 */
	        public function add(string $key, Language $language) {
	            $this->files[$key] = $language;
	        }

			/**
			 * @return Language
			 */
	        public function getLanguage(string $key): Language {
	            if($this->contains($key)) {
					return $this->files[$key];
				}

				return $this->default;
	        }

			/**
			 * @return bool
			 */
			public function contains(string $key): bool {
				if(array_key_exists($key, $this->files)) {
					return true;
				}

				return false;
			}

			/**
			 * @return bool
			 */
			public function exists(string $key): bool {
				if($this->contains($key)) {
					if($this->files[$key]->exists()) {
						return true;
					}
				}

				return false;
			}
	    }
	}
