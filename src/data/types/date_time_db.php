<?php

namespace Agility\Data\Types;

use Agility\Extensions\Chrono\Chronometer;

	class DateTimeDb extends Base {

		const CurrentTimestamp = "CURRENT_TIMESTAMP";

		function cast($value) {

			if (empty($value)) {
				return null;
			}

			if (!is_a($value, Chronometer::class)) {

				$object = Chronometer::new($value);
				return $object;

			}

			return $value;

		}

		function serialize($value) {

			if (empty($value)) {
				return null;
			}

			$format = "Y-m-d H:i:s";
			if (!empty($this->precision)) {

				if ($this->precision == "3") {
					$format .= ".v";
				}
				else {
					$format .= ".u";
				}

			}

			return $value->format($format);

		}

		function __toString() {
			return "datetime";
		}

	}

?>