<?php

namespace Agility\Data\Validations;

use AttributeHelper\Accessor;
use StringHelpers\Str;

	class Base {

		use Accessor;

		protected $attribute;
		protected $message;

		protected static $_validators = [
			"ExclusionValidator",
			"FormatValidator",
			"InclusionValidator",
			"LengthValidator",
			"NumericalityValidator",
			"PresenceValidator",
			"UniquenessValidator",
		];

		function __construct() {
			$this->readonly("attribute", "message");
		}

		protected static function getStorableName($name) {
			return Str::camelCase((new Arrays(explode("_", Str::snakeCase($name))))->firstFew(-1)->implode("_"));
		}

		static function isAvailable($name) {

			$name = str_replace("validates", "", $name);
			$name = static::getStorableName($name);

			if (in_array($name."Validator", static::$_validators)) {
				return $name."Validator";
			}
			else if (in_array($name, static::$_validators)) {
				return $name;
			}

			return false;

		}

		static function register($validator) {

			if (!in_array($validator, static::$_validators)) {
				static::$_validators[] = $validator;
			}

		}

	}

?>