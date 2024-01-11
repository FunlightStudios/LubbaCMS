<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Models\Furniture {
	    use Kitty\System\Patterns\MVC\Models\AbstractFactory;
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

		use Kitty\System\Common;
		use Kitty\System\Configurations\Database;

		use \PDO;

	    final class FurnitureFactory extends AbstractFactory {
	        public function __construct(ModelsManager $modelsManager, PDO $connection) {
				$database = new Database(Common::getConfiguration()->getDatabaseSettings());
				$prefix = $database->getPrefix();

	            parent::__construct($modelsManager, $connection, $prefix . 'furnitures');
	        }

	        public function create($row): Furniture {
	            return new Furniture($row, $this->getConnection(), $this->getModelsManager(), $this->getTable());
	        }
	    }
	}
