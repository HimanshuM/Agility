<?php

namespace Agility\Parser\Objects;

	abstract class Type {

		public $type;
		public $token;

		function token() {
			return $this->token;
		}

		abstract function resolveFor($value);

	}

?>