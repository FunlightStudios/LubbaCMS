<?php
	/**
	 * @author Candan Tombas
	 */

	use Kitty\System\Exceptions\KittyException;

	try {
		require_once __DIR__ . '/Kitty/Kitty.php';
	} catch(KittyException $ex) {
		echo $ex->getMessage();
	} catch(Exception $ex) {
		echo $ex->getMessage();
	}
