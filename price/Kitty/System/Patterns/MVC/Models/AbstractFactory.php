<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Models {
	    use Kitty\System\Exceptions\KittyException;
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

		use \PDO;
		use \PDOException;

	    abstract class AbstractFactory {
	        abstract public function create($row);

	        private $modelsManager;
	        private $connection;

	        private $table;

	        private $cachedRows = [];

			/**
			 * @param ModelsManager $modelsManager
			 * @param PDO           $connection
			 * @param string        $table
			 */
	        public function __construct(ModelsManager $modelsManager, PDO $connection, string $table = '') {
	            $this->modelsManager = $modelsManager;
	            $this->connection = $connection;

	            $this->table = $table;
	        }

			/**
			 * @return string
			 */
	        public function getTable(): string {
	            return $this->table;
	        }

			/**
			 * @return PDO
			 */
	        public function getConnection(): PDO {
	            return $this->connection;
	        }

			/**
			 * @param 	array $params
			 * @return 	mixed
			 */
	        public function _create(array $params) {
	            $queryString = 'INSERT INTO `' . $this->table . '` SET ';
	            $executeParams = [];

	            $i = 0;
	            foreach($params as $param => $val) {
	                $queryString .= '`' . $param . '` = :'.$param;
	                $executeParams[':' . $param] = $val;

	                if(!($i + 1 == count($params))) {
	                    $queryString .= ',';
	                }

	                $i++;
	            }

	            try {
	                $query = $this->connection->prepare($queryString);
	                $query->execute($executeParams);

	                return $this->getById($this->connection->lastInsertId());
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }
	        }

			/**
			 * @return ModelsManager
			 */
	        public function getModelsManager(): ModelsManager {
	            return $this->modelsManager;
	        }

			/**
			 * @param  bool 	$reload
			 * @return mixed
			 */
	        public function getAll(bool $reload = false) {
	            if($this->table == null) {
	                throw new KittyException(__DIR__ . '/AbstractFactory.php', 28, 'Cannot get object by not given table.');
	                return;
	            }

	            if(!$reload) {
	                foreach($this->cachedRows as $_row) {
	                    if($_row->getRow()->$row == $val) {
	                        return $row;
	                    }
	                }
	            }

	            try {
	                $query = $this->connection->query('SELECT * FROM `' . $this->table . '`');

					$objects = [];
	                while($row = $query->fetchObject()) {
	                    $object = $this->create($row);
						$objects[] = $object;
	                }

					return $objects;
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }

	            return null;
	        }

			/**
			 * @param  string 	$row
			 * @param  string 	$val
			 * @param  bool 	$reload
			 * @return mixed
			 */
	        public function getAllByColumn(string $row, string $val, bool $reload = false) {
	            if($this->table == null) {
	                throw new KittyException(__DIR__ . '/AbstractFactory.php', 28, 'Cannot get object by not given table.');
	                return;
	            }

	            if(!$reload) {
	                foreach($this->cachedRows as $_row) {
	                    if($_row->getRow()->$row == $val) {
	                        return $row;
	                    }
	                }
	            }

	            try {
	                $query = $this->connection->prepare('SELECT * FROM `' . $this->table . '` WHERE `' . $row . '` = :val');
	                $query->execute([':val' => $val]);

					$objects = [];
	                while($row = $query->fetchObject()) {
	                    $object = $this->create($row);
						$objects[] = $object;
	                }

					return $objects;
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }

	            return null;
	        }

			/**
			 * @param  string 	$row
			 * @param  string 	$val
			 * @param  bool 	$reload
			 * @return mixed
			 */
	        public function getByColumn(string $row, string $val, bool $reload = false) {
	            if($this->table == null) {
	                throw new KittyException(__DIR__ . '/AbstractFactory.php', 28, 'Cannot get object by not given table.');
	                return;
	            }

	            if(!$reload) {
	                foreach($this->cachedRows as $_row) {
	                    if($_row->getRow()->$row == $val) {
	                        return $row;
	                    }
	                }
	            }

	            try {
	                $query = $this->connection->prepare('SELECT * FROM `' . $this->table . '` WHERE `' . $row . '` = :val');
	                $query->execute([':val' => $val]);

	                if ($query->rowCount() > 0) {
	                    $row = $query->fetchObject();

	                    $object = $this->create($row);

	                    return $object;
	                }
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }

	            return null;
	        }

			/**
			 * @param  int $id
			 * @param  bool $reload
			 * @return mixed
			 */
	        public function getById(int $id, bool $reload = false) {
	            return $this->getByColumn('id', $id, $reload);
	        }
	    }
	}
