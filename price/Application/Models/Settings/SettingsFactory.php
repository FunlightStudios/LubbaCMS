<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Models\Settings {
	    use Kitty\System\Patterns\MVC\Models\AbstractFactory;
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

	    use Kitty\System\Common;
		use Kitty\System\Configurations\Database;

		use \PDO;

	    final class SettingsFactory extends AbstractFactory {
			private $sitepath;

			public function __construct(ModelsManager $modelsManager, PDO $connection) {
	            parent::__construct($modelsManager, $connection);

				$database = new Database(Common::getConfiguration()->getDatabaseSettings());
				$prefix = $database->getPrefix();

				$stmt = $this->getConnection()->prepare('SELECT * FROM `' . $prefix . 'settings`');
				$stmt->execute();

				while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
					$this->{$obj->row} = $obj->value;
				}
		    }

	        public function create($row) {
				return null;
			}

			public function getSiteName(): string {
				return $this->SiteName;
			}

			public function getSitePath(): string {
				return $this->SitePath;
			}

			public function getWebPath(): string {
				return $this->WebPath . '/' . $this->getStyle();
			}

			public function getFurnituresPath(): string {
				return $this->FurnituresPath;
			}

			public function getLanguage(): string {
				return $this->Language;
			}

			public function getTemplate(): string {
				return $this->Template;
			}

			public function getStyle(): string {
				return $this->Style;
			}
	    }
	}
