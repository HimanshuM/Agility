<?php

namespace Agility\Data\Types;

	abstract class Base {

		protected $limit;
		protected $precision;
		protected $scale;

		protected static $registeredTypes = [];

		const ValidTypes = [
			"boolean" => '/bool/',
			"integer" => '/int(eger)?(\[\d+\])?/',
			"float" => '/float(\[\d+(,\d+)?\])?/',
			"double" => '/double(\[\d+(,\d+)?\])?/',
			"str" => '/string(\[\d+\])?/',
			"text" => '/text(\[\d+\])?/',
			"enum" => '/enum(\[[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(,[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*\])?/',
			"datetime" => '/datetime(\[\d+\])/',
			"date" => '/date/',
			"timestamp" => '/timestamp(\[\d+\])/',
			"binary" => '/binary(\[\d+\])?/',
			"reference" => '/references(\[\w+\])?/',
		];

		function __construct() {

		}

		// User for cast objects from setters
		abstract function cast($value);

		static function getType($name, $size = null) {

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

		function options() {

			if ($this->fieldSize != static::DefaultFieldSize) {
				return "[\"size\" => ".$this->fieldSize."]";
			}

			return false;

		}

		static function register($typeName) {

			if (!in_array($typeName, Base::$registeredTypes)) {
				Base::$registeredTypes[] = $typeName;
			}

		}

		// Used for casting objects to database types
		abstract function serialize($value);

		abstract function __toString();

		// Used for casting objects from database types
		function unserialize($value) {
			return $this->cast($value);
		}

	}

?>