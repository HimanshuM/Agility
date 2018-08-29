<?php

namespace Agility\Data\Validations;

	class FormatValidator extends Base {

		function __construct($attribute, $options) {

			if (!$options->exists("format")) {
				throw new Exceptions\MissingValidationAttributeException(FormatValidator::class, "format");
			}

			parent::__construct($attribute, $options);

		}

		function validate($object) {

			$attribute = $this->attribute;
			if (isset($object->$attribute) && preg_match($this->options["format"], $object->$attribute) == false) {
				$object->errors[$attribute][] = $this->message ?? "$attribute must follow '".$this->options["format"]."' format";
			}

		}

	}

?>