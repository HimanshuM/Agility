<?php

namespace Agility\Data\Validations;

	class UniquenessValidator extends Base {

		function validate($object) {

			$attribute = $this->attribute;
			if ($object->isSet($attribute)) {

				$class = get_class($object);
				if (!$class::findBy($attribute, $object->$attribute)->empty) {
					$object->errors->add($attribute, $this->message ?? $object->$attribute." has already been taken");
				}

			}

		}

	}

?>