<?php

namespace Agility\Data\Validations;

use ArrayUtils\Arrays;

	trait Validate {

		public $errors;

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
			static::_getValidations()->runOnCreateValidations($this);
			$this->_performValidationsOnSave();

			$this->_runCallbacks("afterValidationOnCreate");

		}

		protected function _performValidationsOnSave() {

			$this->_runCallbacks("beforeValidationOnSave");

			// Do validations
			static::_getValidations()->runOnSaveValidations($this);

			$this->_runCallbacks("afterValidationOnSave");

		}

		protected function _performValidationsOnUpdate() {

			$this->_runCallbacks("beforeValidationOnUpdate");

			// Do validations
			static::_getValidations()->runOnUpdateValidations($this);
			$this->_performValidationsOnSave();

			$this->_runCallbacks("afterValidationOnUpdate");

		}

		static function validates($column, $with, $options = []) {
			static::_getValidations()->addValidation($column, $with, $options);
		}

		static function validatesWith($name, $args) {

		}

	}

?>