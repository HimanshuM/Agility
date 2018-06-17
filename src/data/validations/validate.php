<?php

namespace Agility\Data\Validations;

use ArrayUtils\Arrays;

	trait Validate {

		protected static $_validations;

		protected static function _getValidations() {

			if (empty(static::$_validations)) {
				static::$_validations = new Arrays;
			}

			if (!static::$_validations->exists(static::class)) {
				static::$_validations[static::class] = new Validations;
			}

			return static::$_validations[static::class];

		}

		protected function _performValidations($create = false) {

			$this->_runCallbacks("beforeValidation");

			// Do validations

			if ($create) {
				$this->_performValidationsOnCreate();
			}
			else {
				$this->_performValidationsOnUpdate();
			}

			$this->_runCallbacks("afterValidation");

		}

		protected function _performValidationsOnCreate() {

			$this->_runCallbacks("beforeValidationOnCreate");

			// Do validations

			$this->_runCallbacks("afterValidationOnCreate");

		}

		protected function _performValidationsOnUpdate() {

			$this->_runCallbacks("beforeValidationOnUpdate");

			// Do validations

			$this->_runCallbacks("afterValidationOnUpdate");

		}

		static function validatesWith($name, $args) {

			if (empty($args)) {
				return;
			}

			static::_getValidations($name, $args);

		}

	}

?>