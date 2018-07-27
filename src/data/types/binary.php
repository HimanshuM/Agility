<?php

namespace Agility\Data\Types;

	class Binary extends Base {

		function __construct($size = null) {

			parent::__construct();
			$this->limit = $size;

		}

		function cast($value) {
			return strval($value);
		}

		function serialize($value) {
			return strval($value);
		}

		function __toString() {
			return "binary";
		}

	}

?>