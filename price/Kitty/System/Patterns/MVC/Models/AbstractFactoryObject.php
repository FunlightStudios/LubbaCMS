<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Models {
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

		use \PDO;
		use \PDOException;

	    class AbstractFactoryObject {
	        private $row;
	        private $table;
	        private $connection;
	        private $modelsManager;

			/**
			 * @param string        $row
			 * @param PDO           $connection
			 * @param ModelsManager $modelsManager
			 * @param string        $table
			 */
	        public function __construct($row, PDO $connection, ModelsManager $modelsManager, string $table = '') {
	            $this->row = $row;
	            $this->connection = $connection;
	            $this->modelsManager = $modelsManager;
	            $this->table = $table;
	        }

	        public function remove() {
	            try {
	                $query = $this->getConnection()->prepare('DELETE FROM `' . $this->getTable() . '` WHERE `id` = :id');
	                $query->execute([':id' => $this->row->id]);
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }
	        }

			/**
			 * @return mixed
			 */
	        public function getRow() {
	            return $this->row;
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
			 * @return ModelsManager
			 */
	        public function getModelsManager(): ModelsManager {
	            return $this->modelsManager;
	        }

			/**
			 * @param string $row
			 * @param string $val
			 */
	        public function set(string $row, string $val) {
	            try {
	                $query = $this->getConnection()->prepare('UPDATE `' . $this->getTable() . '` SET `' . $row . '` = :val WHERE `id` = :id');
	                $query->execute([':id' => $this->getRow()->id, ':val' => $val]);

	                $this->updateRowByColumn('id', $this->getRow()->id);
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            } finally {
	                return $this;
	            }
	        }

			/**
			 * @param  string $column
			 * @param  string $val
			 * @return mixed
			 */
	        public function updateRowByColumn(string $column, string $val) {
	            try {
	                $query = $this->getConnection()->prepare('SELECT * FROM `' . $this->getTable() . '` WHERE `' . $column . '` = :val');
	                $query->execute([':val' => $val]);

	                if($query->rowCount() > 0) {
	                    $this->row = $query->fetchObject();
	                    return true;
	                }
	            } catch(PDOException $ex) {
	                echo $ex->getMessage();
	            }
	        }
	    }
	}
