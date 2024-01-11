<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System {
		use Kitty\System\Configurations\Paths;
		use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;
		use Application\Models\Settings\SettingsFactory;

	    class Common {
	        private static $configuration;
		    private static $modelsManager;

			private static $settingsFactory = null;

			/**
			 * Initializes the class objects.
			 * @param Configuration $configuration
			 * @param PDO           $connection
			 */
	        public static function init(Configuration $configuration, ModelsManager $modelsManager) {
	            self::$configuration = $configuration;
		        self::$modelsManager = $modelsManager;
	        }

			/**
			 * Returns the configuration.
			 * @return Configuration
			 */
			public static function getConfiguration(): Configuration {
				return self::$configuration;
			}

			/**
			 * @return SettingsFactory
			 */
			public static function getSettingsFactory(): SettingsFactory {
				if(self::$settingsFactory == null) {
					self::$settingsFactory = self::$modelsManager->get(SettingsFactory::class);
				}

				return self::$settingsFactory;
			}

			/**
			 * Returns a boolean.
			 * @param  mixed $val
			 * @return bool
			 */
			public static function isDecimal($val) {
				return is_numeric($val) && floor($val) != $val;
			}

			/**
			 * Redirects the user.
			 * @param string $url
			 */
	        public static function redirect(string $url) {
				$paths = new Paths(self::$configuration->getPaths());
	            header('Location: ' . self::getSettingsFactory()->getSitePath() . '/' . $url);
	        }

			/**
	         * This function checks if the string a valid username.
	         * @param string $username
	         * @return boolean
	         */
			public static function isValidUsername(string $username): bool {
				if(ctype_alnum($username)) {
					return true;
				}

				return false;
			}

			/**
			 * This function checks if the string a valid email.
			 * @param string $email
			 * @return boolean
			 */
			public static function isValidMail(string $email): bool {
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					return false;
				}

				return true;
			}

			/**
			 * Returns the ip address of the user.
			 * @return string
			 */
			public static function getIp(): string {
				$ip = $_SERVER['REMOTE_ADDR'];

				if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
					$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
				}

				return $ip;
			}

			/**
			 * Returns the country code of the user.
			 * @return string
			 */
			public static function getCountryCode(): string {
				if(!function_exists('geoip_open')) return '';

				$paths = new Paths(self::$configuration->getPaths());

				$gi = geoip_open($paths->getFrameworkPath() . '\GeoIP.dat', GEOIP_MEMORY_CACHE);
				$country = geoip_country_code_by_addr($gi, self::getIp());
				geoip_close($gi);

				return strtoupper($country);
			}
	    }
	}
