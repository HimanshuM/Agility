<?php

namespace Agility\Data\Types;

	class Enum extends Base {

		protected $values = [];

		function __construct($values = null) {

			parent::__construct();

			if (!empty($values)) {

				$values = explode(",", $values);
				foreach ($values as $value) {
					$this->values[] = trim($value);
				}

			}

		}

		function cast($value) {
			return $value;
		}

		function options() {
			return "[".implode(", ", $this->values)."]";
		}

		function serialize($value) {
			return $value;
		}

		function __toString() {
			return "enum";
		}

	}

?>