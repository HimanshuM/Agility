<?php

namespace Agility\Data\Types;

	class Boolean extends Base {

		function __construct() {
			parent::__construct();
		}

		function cast($value) {
			return boolval($value);
		}

		function serialize($value) {
			return boolval($value);
		}

		function __toString() {
			return "boolean";
		}

	}

?>