<?php

namespace Agility\Data\Helpers;

use Agility\Data\Validations;
use Agility\Exceptions\PropertyNotFoundException;

	trait Dispatch {

		protected function defaultCallback($name, $value = nil) {

			if ($this->_hasAttribute($name)) {

				if ($value == nil) {
					return $this->_getAttribute($name);
				}
				else {
					return $this->_setAttribute($name, $value);
				}

			}
			else if (static::belongsToAssociations()->exists($name)) {
				return $this->belongsToAssociations()[$name]->fetch($this);
			}
			else if (static::hasManyAssociations()->exists($name)) {
				return static::hasManyAssociations()[$name]->prepare($this);
			}
			else if (static::hasAndBelongsToManyAssociations()->exists($name)) {

			}
			else if (static::hasOneAssociations()->exists($name)) {

			}
			else {
				throw new PropertyNotFoundException($name, static::class);
			}

		}

		function __call($name, $args = []) {

			if (empty($args)) {
				return $this->_getAttribute($name);
			}
			else {
				return $this->_setAttribute($name, $args[0]);
			}

		}

		static function __callStatic($name, $args = []) {

			if (strpos($name, "findBy") === 0) {
				return static::findByResolver(substr($name, strlen("findBy")), $args);
			}
			else if (in_array($name, static::$CALLBACKS)) {
				return static::_addToCallbacks($name, $args);
			}
			else if (static::hasScope($name)) {
				return static::tryScope($name, $args);
			}
			else if (($validator = Validations\Base::isAvailable($name)) !== false) {
				return static::validatesWith($validator, $args);
			}

		}

	}

?>