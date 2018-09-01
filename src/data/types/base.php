<?php

namespace Agility\Data\Types;

	abstract class Base {

		protected $limit = null;
		protected $precision = null;
		protected $scale = null;

		protected static $registeredTypes = [];

		const ValidTypes = [
			"binary" => '/binary(\[\d+\])?/',
			"boolean" => '/bool/',
			"datetime" => '/datetime(\[\d+\])/',
			"date" => '/date/',
			"double" => '/double(\[\d+(,\s*\d+)?\])?/',
			"enum" => '/enum(\[[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(,[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*\])?/',
			"float" => '/float(\[\d+(,\d+)?\])?/',
			"integer" => '/int(eger)?(\[\d+\])?/',
			"reference" => '/references(\[\w+\])?/',
			"str" => '/string(\[\d+\])?/',
			"text" => '/text(\[\d+\])?/',
			"timestamp" => '/timestamp(\[\d+\])/',
			"uint" => '/uint(\[\d+\]?)/'
		];

		function __construct() {

		}

		// User for cast objects from setters
		abstract function cast($value);

		static function getType($name, $size = null) {

			$name = $name == "string" ? "str" : $name;

			if (in_array($name, Base::$registeredTypes)) {
				return new $name($size);
			}
			else if (file_exists(__DIR__."/$name.php")) {

				$name = "\\Agility\\Data\\Types\\".ucfirst(strtolower($name));
				return new $name($size);

			}
			else if (file_exists(__DIR__."/".$name."_db.php")) {

				$name = "\\Agility\\Data\\Types\\".ucfirst(strtolower($name))."Db";
				return new $name($size);

			}
			else {
				throw new SqlTypeNotFoundException($name);
			}

		}

		function nativeType($typeMapper) {
			return $typeMapper->getNativeType($this->__toString(), $this->limit, $this->precision, $this->scale);
		}

		function options() {

			return [
				"limit" => $this->limit,
				"precision" => $this->precision,
				"scale" => $this->scale
			];

		}

		static function register($typeName) {

			if (!in_array($typeName, Base::$registeredTypes)) {
				Base::$registeredTypes[] = $typeName;
			}

		}

		// Used for casting objects to database types
		abstract function serialize($value);

		function setParameters($params = []) {

			if (!empty($params["limit"])) {
				$this->limit = $params["limit"];
			}
			if (!empty($params["precision"])) {
				$this->precision = $params["precision"];
			}
			if (!empty($params["scale"])) {
				$this->scale = $params["scale"];
			}

		}

		abstract function __toString();

		// Used for casting objects from database types
		function unserialize($value) {
			return $this->cast($value);
		}

	}

?>