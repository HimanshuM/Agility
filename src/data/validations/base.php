<?php

namespace Agility\Data\Validations;

use ArrayUtils\Arrays;
use AttributeHelper\Accessor;
use StringHelpers\Str;

	class Base {

		use Accessor;

		protected $attribute;
		protected $message;
		protected $options;
		protected $validator;

		protected static $_validators = [
			"exclusion",
			"format",
			"inclusion",
			"length",
			"numericality",
			"presence",
			"uniqueness",
		];

		function __construct($attribute, $options, $validator = false) {

			$this->attribute = $attribute;
			$this->message = $options->delete("message");
			$this->options = $options;
			$this->validator = $validator;

			$this->readonly("attribute", "message");

		}

		protected static function getStorableName($name) {
			return Str::pascalCase((new Arrays(explode("_", Str::snakeCase($name))))->firstFew(-1)->implode("_"));
		}

		static function isAvailable($name, $onlyName = false) {

			$name = str_replace("validates", "", $name);
			$name = str_replace("Of", "", $name);
			// $name = static::getStorableName($name);
			$name = Str::snakeCase($name);

			if (in_array($name, static::$_validators)) {

				if (!$onlyName) {
					return "Agility\\Data\\Validations\\".Str::camelCase($name."_validator");
				}

				return $name;

			}

			return false;

		}

		static function register($validator) {

			if (!in_array($validator, static::$_validators)) {
				static::$_validators[] = $validator;
			}

		}

		function validate($object) {

		}

	}

?>