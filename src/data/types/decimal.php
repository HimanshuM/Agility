<?php

namespace Agility\Data\Types;

	class Decimal extends Base {

		function __construct($size = null) {

			parent::__construct();

			if (!empty($size)) {

				$size = explode(",", $size);
				if (count($size) == 1) {
					$this->scale = $size[0];
				}
				else {

					$this->precision = $size[0];
					$this->scale = $size[1];

				}

			}

		}

		function cast($value) {
			return doubleval($value);
		}

		function serialize($value) {

			if (is_null($value)) {
				return $value;
			}

			return doubleval($value);

		}

		function __toString() {
			return "decimal";
		}

	}

?>