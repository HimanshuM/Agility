<?php

namespace Agility\Data\Types;

	class Reference extends Base {

		protected $polymorphic = false;
		protected $foreignKey = false;

		function __construct($size) {

			parent::__construct();
			if ($size == "polymorphic") {
				$this->polymorphic = true;
			}
			else {

				$this->foreignKey = $size;
				$this->fieldSize = "foreignKey";

			}

		}

		static function getType($fieldSize = null) {
			return parent::getType("reference", $fieldSize);
		}

		function options() {
			return "[\"".$this->fieldSize."\" => ".($this->fieldSize == "polymorphic" ? "true" : "\"".$this->foreignKey."\"")."]";
		}

		function __toString() {
			return "references";
		}

	}

?>