<?php

namespace Agility\Data\Schema;

use Agility\Data\Collection;
use Agility\Exceptions;
use InvalidArgumentException;

	trait Attributes {

		protected $attributes;

		function addSubObject($name, $object) {
			$this->attributes->$name = $object;
		}

		function fetchAttributes($noCasting = true) {

			if ($noCasting) {
				return $this->attributes->toArray;
			}

			$collection = $this->attributes->toArray;
			$return = [];
			foreach ($collection as $name => $value) {

				if (isset(static::attributeObjects()[$name])) {
					$value = static::attributeObjects()[$name]->dataType->serialize($value);
				}
				else {
					$value = static::generatedAttributes()[$name]->dataType->serialize($value);
				}

				$return[$name] = $value;

			}

			return $return;

		}

		function fillAttributes($collection, $forcible = true) {

			if (is_a($collection, Collection::class)) {
				$collection = $collection->toArray;
			}
			else if (!is_array($collection)) {
				throw new InvalidArgumentException("Array or an object of type Agility\\Data\\Collection is expected", 1);
			}

			foreach ($collection as $name => $value) {

				if ($forcible !== false) {

					if (!in_array($name, $this->_accessibleAttributes)) {
						throw new BatchUpdateException($name, static::class);
					}

				}
				else {
					$this->_fresh = false;
				}

				if (isset(static::attributeObjects()[$name])) {
					$value = static::attributeObjects()[$name]->dataType->unserialize($value);
				}
				else {
					$value = static::generatedAttributes()[$name]->dataType->unserialize($value);
				}

				$this->attributes->$name = $value;

			}

			if ($forcible === false) {
				$this->_runCallbacks("afterFind");
			}

		}

		private function _getAttribute($name) {

			if (!$this->_hasAttribute($name)) {
				return null;
			}

			return $this->attributes->$name;

		}

		private function _hasAttribute($name) {
			return static::generatedAttributes()->exists($name);
		}

		private function _setAttribute($name, $value) {

			if (isset(static::attributeObjects()[$name])) {
				$value = static::attributeObjects()[$name]->dataType->cast($value);
			}

			$this->attributes->$name = $value;
			$this->_dirty = true;

		}

	}

?>