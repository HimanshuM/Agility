<?php

namespace Agility\Data\Relations;

	trait QueryMethods {

		static function includes($table) {
			return static::initializeRelation()->includes($table);
		}

		static function fullJoin($table) {
			return static::join($table, "FullJoin");
		}

		static function groupBy() {
			return call_user_func_array([static::initializeRelation(), "groupBy"], func_get_args());
		}

		static function innerJoin($table) {
			return static::join($table);
		}

		static function join($table, $join = "InnerJoin") {
			return static::initializeRelation()->join($table, $join);
		}

		static function leftJoin($table) {
			return static::join($table, "LeftJoin");
		}

		static function orderBy() {
			return call_user_func_array([static::initializeRelation(), "orderBy"], func_get_args());
		}

		static function select() {
			return call_user_func_array([static::initializeRelation(), "select"], func_get_args());
		}

		static function skip($offset) {
			return static::initializeRelation()->skip($offset);
		}

		static function take($length) {
			return static::initializeRelation()->take($length);
		}

		static function where($clause, $params = []) {
			return static::initializeRelation()->where($clause, $params);
		}

	}

?>