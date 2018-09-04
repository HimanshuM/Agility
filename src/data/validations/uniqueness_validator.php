<?php

namespace Agility\Data\Validations;

	class UniquenessValidator extends Base {

		function validate($object) {

			$attribute = $this->attribute;
			if ($object->isSet($attribute)) {

				$class = get_class($object);
				if (!empty($other = $class::findBy($attribute, $object->$attribute))) {

					if ($object->fresh || $other->first->valueOfPrimaryKey() != $object->valueOfPrimaryKey()) {
						$object->errors->add($attribute, $this->message ?? $object->$attribute." has already been taken");
					}

				}

			}

		}

	}

?>