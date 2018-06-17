<?php

namespace Agility\Data\Connection;

use Agility\Data\Connection\SqlConnectionFailedException;
use Exception;
use PDO;

	class Base {

		public $prefix = "";
		public $suffix = "";

		function __construct($connectionArray) {

			if (!empty($connectionArray["prefix"])) {
				$this->prefix = $connectionArray["prefix"];
			}
			if (!empty($connectionArray["suffix"])) {
				$this->prefix = $connectionArray["suffix"];
			}

		}

		protected function getPdoConnection($dsn, $username, $password, $config = []) {

			try {
				return new PDO($dsn, $username, $password, $config);
			}
			catch (Exception $e) {
				throw new SqlConnectionFailedException($e->getMessage());
			}

		}

	}

?>