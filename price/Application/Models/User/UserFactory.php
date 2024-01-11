<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Models\User {
	    use Kitty\System\Patterns\MVC\Models\AbstractFactory;
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

		use Kitty\System\Common;
		use Kitty\System\Configurations\Database;

		use \PDO;

	    final class UserFactory extends AbstractFactory {
	        public function __construct(ModelsManager $modelsManager, PDO $connection) {
				$database = new Database(Common::getConfiguration()->getDatabaseSettings());
				$prefix = $database->getPrefix();

	            parent::__construct($modelsManager, $connection, $prefix . 'users');
	        }

			public function getHashedString($string): string {
				return sha1($string);
			}

	        public function create($row): User {
	            return new User($row, $this->getConnection(), $this->getModelsManager(), $this->getTable());
	        }
	    }
	}
