<?php

namespace Agility\Parser\Objects;

	class Arrays extends Type {

		protected $name = false;
		protected $arr = [];
		public $type = "array";

		function __construct($arr) {
			$this->arr = $arr;
		}

		function __toString() {

			$values = [];
			foreach ($this->arr as $key => $value) {
				$values[] = "$key => $value";
			}

			return "[".implode(", ", $values)."]";

		}

		function resolveFor($value) {

			$index = $this->arr[0];
			if (is_a($value, Arrays::class)) {
				return $value->arr[$index];
			}
			if (!is_a($value->get(), Arrays::class)) {
				throw new \Exception("Cannot get index '$index' of type ".gettype($value));
			}

			return $value->get()->arr[$index];

		}

	}

?>