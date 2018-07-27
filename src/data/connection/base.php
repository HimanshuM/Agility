<?php

namespace Agility\Data\Connection;

use Agility\Data\Connection\SqlConnectionFailedException;
use Exception;
use PDO;

	abstract class Base {

		public $prefix = "";
		public $suffix = "";

		const FetchIndexedColumns = 4;

		const DDLStatement = 10;

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

		abstract function getTypeMapper();

	}

?>