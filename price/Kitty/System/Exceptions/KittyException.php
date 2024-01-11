<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Exceptions {
		use \Exception;

		final class KittyException extends Exception {
			private $object;

			public function __construct(string $file, int $line, string $message, $object = null) {
				$this->object = $object;
				parent::__construct($file . ' on line ' . $line . ': ' . $message, 0, null);
			}

			public function getObject() {
				return $this->object;
			}
		}
	}
