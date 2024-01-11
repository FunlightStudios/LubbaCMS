<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System {
		use Kitty\System\Exceptions\KittyException;

		final class Configuration {
			private $configuration = [];

			/**
			 * @param Array $config
			 */
			public function __construct($configuration) {
				if(is_object($configuration)) {
					$this->configuration = $configuration;
				} else {
					throw new KittyException(__DIR__ . '\Configuration.php', 9, '$configuration has to be an object, ' . gettype($configuration) . ' given.');
				}
			}

			/**
			 * Returns the mode.
			 * @return string
			 */
			public function getMode(): string {
				return $this->configuration->mode;
			}

			/**
			 * Returns if the country auto detect enabled or disabled.
			 * @return bool
			 */
			public function getCountryAutoDetect(): bool {
				return (bool) $this->configuration->country_auto_detect;
			}

			/**
			 * Returns the save path for the temps.
			 * @return string
			 */
			public function getSessionSavePath(): string {
				return (bool) $this->configuration->session_save_path;
			}

			/**
			 * Returns the prefix for the sessions.
			 * @return string
			 */
			public function getSessionPrefix(): string {
				return $this->configuration->session_prefix;
			}
			/**
			 * Returns the application paths.
			 * @return object
			 */
			public function getPaths() {
				return $this->configuration->paths;
			}

			/**
			 * Returns the database settings.
			 * @return object
			 */
			public function getDatabaseSettings() {
				return $this->configuration->database;
			}
		}
	}
