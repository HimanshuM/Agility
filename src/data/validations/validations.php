<?php

namespace Agility\Data\Validations;

use ArrayUtils\Arrays;
use AttributeHelper\Accessor;

	class Validations {

		use Accessor;

		protected $validationsOnCreate;
		protected $validationsOnSave;
		protected $validationsOnUpdate;

		function __construct() {

			$this->validationsOnCreate = new Arrays;
			$this->validationsOnSave = new Arrays;
			$this->validationsOnUpdate = new Arrays;

			$this->readonly("validationsOnCreate", "validationsOnSave", "validationsOnUpdate");

		}

		function validatesWith($validator, $args) {

			list($attributes, $options) = $this->compileArgs($args);

			$on = strtolower($options["on"] ?? "save");
			if ($on == "create") {
				$on = "Create";
			}
			else if ($on == "update") {
				$on = "Update";
			}
			else {
				$on = "Save";
			}

			$validationType = "validationsOn".$on;

			foreach ($attributes as $attribute) {
				$this->$validationType[] = new $validator($attributes, $options);
			}

		}

		protected function compileArgs($args) {

			$attributes = [];
			$options = [];
			foreach ($args as $arg) {

				if (is_array($arg)) {
					$options = $arg;
				}
				else {
					$attributes[] = $arg;
				}

			}

			return [$attributes, $options];

		}

	}

?>