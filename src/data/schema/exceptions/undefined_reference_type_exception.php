<?php

namespace Agility\Data\Schema\Exceptions;

use Exception;

	class UndefinedReferenceTypeException extends Exception {

		function __construct($sourceTable, $referencedTable) {
			parent::__construct("Undefined reference type to $referencedTable from $sourceTable");
		}

	}

?>