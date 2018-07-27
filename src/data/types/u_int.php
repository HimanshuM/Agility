<?php

namespace Agility\Data\Types;

	class UInt extends Integer {

		function cast($value) {
			return abs($value);
		}

		function serialize($value) {
			return abs($value);
		}

		function __toString() {
			return "uint";
		}

	}

?>