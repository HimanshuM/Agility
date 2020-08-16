<?php

namespace Agility\Parser;

	class SymbolTable {

		static protected $symTab = [];

		static function insertIfNot($token) {

			if (!isset(SymbolTable::$symTab[$token->token])) {
				return SymbolTable::$symTab[$token->token] = new Value($token);
			}

		}

		static function get($token) {
			return SymbolTable::$symTab[$token->token] ?? false;
		}

		static function getOrInsert($token) {

			$value = SymbolTable::get($token);
			if ($value === false) {
				return SymbolTable::insertIfNot($token);
			}

			return $value;

		}

		static function print() {
			var_dump(SymbolTable::$symTab);
		}

	}