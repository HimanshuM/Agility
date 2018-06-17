<?php

namespace Agility\Data\Connection\Mysql;

use Agility\Data\Connection\AbstractType;
use Agility\Data\Connection\SqlTypeLengthException;

	class MysqlTypes extends AbstractType {

		const NativeTypes = [
			"boolean"	=> 	["name" => "tinyint", 		"limit" => 1, 		"regex" => '/tinyint/'],
			"uint"		=> 	["name" => "int unsigned", 	"limit" => 10, 		"regex" => '/int(\(\d+\)) unsigned/'],
			"integer"	=> 	["name" => "int", 			"limit" => 11, 		"regex" => '/int(\(\d+\))/'],
			"float"		=> 	["name" => "float", 		"precision" => 10, 	"regex" => '/float(\(\d+(,\d+)?\))?/', "scale" => 2],
			"double"	=> 	["name" => "double", 		"precision" => 10, 	"regex" => '/double(\(\d+(,\d+)?\))?/', "scale" => 0],
			"str"		=> 	["name" => "varchar", 		"limit" => 255, 	"regex" => '/varchar(\(\d+\))/'],
			"text"		=> 	["name" => "text", 			"limit" => 65535, 	"regex" => '/text(\(\d+\))?/'],
			"enum"		=> 	["name" => "enum", 			"limit" => "",	 	"regex" => '/enum(\([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(,[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*\))?/'],
			"datetime"	=> 	["name" => "datetime", 		"precision" => 0, 	"regex" => '/datetime(\(\d+\))?/'],
			"timestamp"	=> 	["name" => "timestamp",		"precision" => 0, 	"regex" => '/timestamp(\(\d+\))?/'],
			"date"		=> 	["name" => "date", 			"limit" => "",	 	"regex" => '/date/'],
			"binary"	=> 	["name" => "blob", 			"limit" => "",	 	"regex" => '/blob(\(\d+\))?/'],
		];

		function getNativeType($type, $limit = null, $precision = null, $scale = null) {

			if ($type == "integer") {
				return $this->getNativeInteger($limit);
			}
			else if ($type == "uint") {
				return $this->getNativeInteger($limit)." unsigned";
			}
			else if ($type == "text") {
				return $this->getNativeText($limit);
			}
			else if ($type == "binary") {
				return $this->getNativeBinary($limit);
			}
			else {
				return parent::getNativeType($type, $limit, $precision, $scale);
			}

		}

		function getNativeInteger($limit = null) {

			if (empty($limit)) {
				return "int";
			}

			if ($limit == 1) {
				return "tinyint";
			}
			else if ($limit < 3) {
				return "smallint";
			}
			else if ($limit < 4) {
				return "mediumint";
			}
			else if ($limit < 5) {
				return "int";
			}
			else if ($limit < 8) {
				return "bigint";
			}

		}

		function getNativeText($limit) {

			if (empty($limit)) {
				return "text";
			}

			if ($limit <= 0xff) {
				return "tinytext";
			}
			else if ($limit <= 0xffff) {
				return "text";
			}
			else if ($limit <= 0xffffff) {
				return "mediumtext";
			}
			else if ($limit <= 0xffffffff) {
				return "longtext";
			}
			else {
				throw new SqlTypeLengthException("text", $limit);
			}

		}

		function getNativeBinary($limit) {

			if (empty($limit)) {
				return "blob";
			}

			if ($limit <= 0xff) {
				return "tinyblob";
			}
			else if ($limit <= 0xffff) {
				return "blob";
			}
			else if ($limit <= 0xffffff) {
				return "mediumblob";
			}
			else if ($limit <= 0xffffffff) {
				return "longblob";
			}
			else {
				throw new SqlTypeLengthException("blob", $limit);
			}

		}

	}

?>