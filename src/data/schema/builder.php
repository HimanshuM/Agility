<?php

namespace Agility\Data\Schema;

use Agility\Data\Schema\Attribute;
use Agility\Data\Types\Base;
use ArrayUtils\Arrays;
use StringHelpers\Str;

	trait Builder {

		private $_attributeObjects;
		private $_accessibleAttributes = [];
		private $_protectedAttributes = [];

		protected function attribute($name, $type, $default = null) {

			$attribute = new Attribute($name, Base::getType($type));
			if ($default !== null && $default !== false) {
				$attribute->defaultValue = $default;
			}

			$this->_attributeObjects[$name] = $attribute;

		}

		protected function attrAccessible() {

			foreach (func_get_args() as $attribute) {

				if (in_array($attribute, $this->_protectedAttributes)) {
					throw new Exception("'$attribute' has already been marked protected", 1);
				}

				$this->_accessibleAttributes[] = $attribute;

			}

		}

		protected function attrProtected() {

			foreach (func_get_args() as $attribute) {

				if (in_array($attribute, $this->_accessibleAttributes)) {
					throw new Exception("'$attribute' has already been marked mass accessible", 1);
				}

				$this->_protectedAttributes[] = $attribute;

			}

		}

		protected static function buildAttribute($attribute) {

			$dataType = Attribute::parseDataType(static::connection()->getTypeMapper(), $attribute->type);

			$unique = $attribute->key == "UNI";
			$index = $attribute->key == "MUL";
			$autoIncrement = strpos($attribute->extra, "auto_increment") !== false;
			$nullable = $attribute->null != "NO";

			$onUpdate = null;
			if (strpos($attribute->extra, "on update") !== false) {

				$fragments = explode(" ", $attribute->extra);
				foreach ($fragments as $i => $fragment) {

					if ($fragment == "update") {

						$onUpdate = $fragments[$i + 1];
						break;

					}

				}

			}

			static::$_metaStore->generatedAttributes[static::class][Str::pascalCase($attribute->field)] = new Attribute($attribute->field, $dataType, $nullable, $attribute->default, $autoIncrement, $index, $unique, $onUpdate);

		}

		protected static function generateAttributes() {

			if (static::$_metaStore->generatedAttributes->exists(static::class)) {
				return;
			}

			static::$_metaStore->generatedAttributes[static::class] = new Arrays;

			$resultSet = static::connection()->execute(static::aquaTable()->describe());
			foreach ($resultSet as $attribute) {
				static::buildAttribute($attribute);
			}

		}

		static function generatedAttributes() {
			return static::$_metaStore->generatedAttributes[static::class];
		}

	}

?>