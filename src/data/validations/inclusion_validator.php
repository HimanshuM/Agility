<?php

namespace Agility\Data\Validations;

use Phpm\Exceptions\TypeExceptions\InvalidTypeException;
use StringHelpers\Inflect;

	class InclusionValidator extends Base {

		function __construct($attribute, $options) {

			if (!$options->exists("in")) {
				throw new Exceptions\MissingValidationAttributeException(InclusionValidator::class, "in");
			}
			if (!is_array($options["in"]) && !is_a($options["in"], Arrays::class)) {
				throw new InvalidTypeException("in", ["array"]);
			}

			parent::__construct($attribute, $options);

		}

		function validate($object) {

			$attribute = $this->attribute;
			if (isset($object->$attribute) && !in_array($object->$attribute, $this->options["in"])) {
				$object->errors[$attribute][] = $this->message ?? "$attribute must be one of ".Inflect::toSentence($this->options["in"], "or");
			}

		}

	}

?>