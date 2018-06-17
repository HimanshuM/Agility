<?php

namespace Agility\Data\Types;

	class Json extends Base {

		function __construct() {
			parent::__construct();
		}

		function cast($value) {

			if (is_string($value)) {
				return json_decode($value, true);
			}

			return $value;

		}

		function serialize($value) {
			return json_encode($value);
		}

		function __toString() {
			return "json";
		}

		function unserialize($value) {
			return json_decode($value, true);
		}

	}

?>