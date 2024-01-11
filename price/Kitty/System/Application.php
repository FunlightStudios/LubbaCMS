<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System {
		use Kitty\System\Utils\Helpers\Multilingual\Multilingual;
		use Kitty\System\Utils\Helpers\Templates\TplManager;

		use \PDO;

		final class Application {
			private $configuration;
			private $connection;
			private $multilingual;
			private $tplManager;

			/**
			 * @param Configuration $config
			 * @param PDO $connection
			 * @param Multilingual $multilingual
			 * @param TplManager $tplManager
			 */
			public function __construct(Configuration $configuration, PDO $connection, Multilingual $multilingual, TplManager $tplManager) {
				$this->configuration = $configuration;
				$this->connection = $connection;

				$this->multilingual = $multilingual;
				$this->tplManager = $tplManager;
			}

			/**
			 * Returns the tpl manager.
			 * @return TplManager
			 */
			public function getTplManager(): TplManager {
				return $this->tplManager;
			}

			/**
			 * Returns the multilingual.
			 * @return Multilingual
			 */
			public function getMultilingual(): Multilingual {
				return $this->multilingual;
			}

			/**
			 * Returns the configuration.
			 * @return Configuration
			 */
			public function getConfiguration(): Configuration {
				return $this->configuration;
			}

			/**
			 * Returns the connection.
			 * @return PDO
			 */
			public function getConnection(): PDO {
				return $this->connection;
			}
		}
	}
