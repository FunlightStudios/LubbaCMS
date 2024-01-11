<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Configurations {
		final class Database {
			private $database;

			/**
			 * @param array $database
			 */
			public function __construct($database) {
				if(is_object($database)) {
					$this->database = $database;
				}
			}

			/**
			 * Returns the username.
			 * @return string
			 */
			public function getUsername(): string {
				return $this->database->username;
			}

			/**
			 * Returns the password.
			 * @return string
			 */
			public function getPassword(): string {
				return $this->database->password;
			}

			/**
			 * Returns the database name.
			 * @return string
			 */
			public function getDatabase(): string {
				return $this->database->database;
			}

			/**
			 * Returns the prefix.
			 * @return string
			 */
			public function getPrefix(): string {
				return $this->database->prefix;
			}

			/**
			 * Returns the hostname.
			 * @return string
			 */
			public function getHostname(): string {
				return $this->database->hostname;
			}

			/**
			 * Returns the database port.
			 * @return int
			 */
			public function getPort(): int {
				return $this->database->port;
			}

			/**
			 * Returns the connections string.
			 * @return string
			 */
			public function getString(): string {
				$string = str_replace('%hostname%', $this->getHostname(), str_replace('%database%', $this->getDatabase(), $this->database->string));
				return $string;
			}
		}
	}
