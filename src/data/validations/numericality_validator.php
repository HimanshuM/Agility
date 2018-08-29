<?php

namespace Agility\Data\Validations;

	class NumericalityValidator extends Base {

		function validate($object) {

			$attribute = $this->attribute;
			if (isset($object->$attribute) && !is_numeric($object->$attribute)) {
				$object->errors[$attribute][] = $this->message ?? "$attribute should be numeric";
			}

		}

	}

?>