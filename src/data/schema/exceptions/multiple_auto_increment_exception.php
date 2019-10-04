<?php

namespace Agility\Data\Schema\Exceptions;

use Exception;

	class MultipleAutoIncrementException extends Exception {

		function __construct($tableName) {
			parent::__construct("Cannot add an auto increment column to table '$tableName', it already has a primary key");
		}

	}

?>