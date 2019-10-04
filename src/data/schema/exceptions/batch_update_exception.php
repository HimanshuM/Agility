<?php

namespace Agility\Data\Schema\Exceptions;

use Exception;

	class BatchUpdateException extends Exception {

		function __construct($attribute, $model) {
			parent::__construct("'$attribute' cannot be batch updated in class '$model'");
		}

	}

?>