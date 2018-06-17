<?php

namespace Agility\Data\Schema;

use Agility\Data\Connection\AbstractType;
use Agility\Data\Types\Base;
use Agility\Data\Types\SqlTypeNotFoundException;
use Agility\Data\Types\Str;
use AttributeHelper\Accessor;

	class Attribute {

		use Accessor;

		protected $_name;
		protected $_dataType;
		protected $_nullable = true;
		protected $_defaultValue = null;
		protected $_autoIncrement = false;
		protected $_indexed = false;
		protected $_unique = false;
		protected $_onUpdate = null;
		protected $_comment = "";

		function __construct($name, $dataType = null, $nullable = true, $defaultValue = null, $autoIncrement = false, $indexed = false, $unique = false, $onUpdate = null, $comment = "") {

			$this->_name = $name;
			if (is_null($dataType)) {
				$dataType = new Str;
			}
			else if (!is_a($dataType, Base::class)) {
				throw new SqlTypeNotFoundException($dataType);
			}
			$this->_dataType = $dataType;
			$this->_nullable = $nullable;
			$this->_defaultValue = $defaultValue;
			$this->_autoIncrement = $autoIncrement;
			$this->_indexed = $indexed;
			$this->_unique = $unique;
			$this->_onUpdate = $onUpdate;
			$this->_comment = $comment;

			$this->prependUnderscore();
			$this->disableStrictAccessibility();

		}

		static function build($name, $properties) {

			$dataType = null;
			$indexed = false;
			$unique = false;
			$autoIncrement = false;
			$modifier = false;
			$references = false;

			foreach ($properties as $index => $property) {

				if ($index == 0) {
					continue;
				}

				if (empty($dataType)) {

					foreach (Base::ValidTypes as $type => $validType) {

						$matches = [];
						if (preg_match($validType, $property, $matches)) {

							$dataType = Base::getType($type, isset($matches[1]) ? $matches[1] : null);
							break;

						}

					}

				}
				if (empty($modifier)) {

					if ($property == "index") {

						$indexed = true;
						$modifier = true;

					}
					else if ($property == "unique") {

						$unique = true;
						$modifier = true;

					}

				}

			}

			return new Attribute($name, $dataType, true, null, false, $indexed, $unique, null, "");

		}

		function __debugInfo() {

			return [
				"name" => $this->_name,
				"dataType" => $this->_dataType."",
				"nullable" => $this->_nullable,
				"defaultValue" => $this->_defaultValue,
				"autoIncrement" => $this->_autoIncrement,
				"indexed" => $this->_indexed,
				"unique" => $this->_unique,
				"onUpdate" => $this->_onUpdate,
				"comment" => $this->_comment,
			];

		}

		static function parseDataType(AbstractType $typesMapper, $dataType) {

			foreach ($typesMapper::NativeTypes as $type => $typeInfo) {

				$matches = [];
				if (preg_match($typeInfo["regex"], $dataType, $matches)) {
					return Base::getType($type, isset($matches[1]) ? $matches[1] : null);
				}

			}

		}

	}

?>