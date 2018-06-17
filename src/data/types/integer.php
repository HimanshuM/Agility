<?php

namespace Agility\Data\Types;

	class Integer extends Base {

		function cast($value) {
			return intval($value);
		}

		function serialize($value) {
			return intval($value);
		}

		function __toString() {
			return "integer";
		}

	}

?>