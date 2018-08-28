<?php

namespace Agility\Data\Validations;

	class PresenceValidator {

		function validate($object) {

			$attribute = $this->attribute;
			if (!isset($object->$attribute)) {
				$object->errors[$attribute][] = $this->message ?? "$attribute is not present";
			}
			else if (empty(trim($object->$attribute))) {
				$object->errors[$attribute][] = $this->message ?? "$attribute cannot be empty";
			}

		}

	}

?>