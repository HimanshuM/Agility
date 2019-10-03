<?php

namespace Agility\Server\Exceptions;

use Agility\Http\Exceptions\HttpException;
use ArrayUtils\Arrays;
use StringHelpers\Inflect;

	class ParameterMissingException extends HttpException {

		function __construct($keys, $arrName = "params") {

			$this->httpStatus = 400;

			if (is_array($keys) || is_a($keys, Arrays::class)) {
				parent::__construct("One or more of ".Inflect::toSentence($keys, "and")." keys not found in $arrName array");
			}
			else {
				parent::__construct("Key ".$keys." not found in $arrName array");
			}

		}

	}

?>