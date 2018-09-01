<?php

namespace Agility\Data\Types;

use Agility\Chrono;

	class Date extends Base {

		function cast($value) {

			if (!is_a($value, Chrono\Date::class)) {
				return new Chrono\Date($value);
			}

			return $value;

		}

		function serialize($value) {
			return $value->date;
		}

		function __toString() {
			return "date";
		}

	}

?>