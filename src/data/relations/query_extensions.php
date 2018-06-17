<?php

namespace Agility\Data\Relations;

use Agility\Data\Relation;

	trait QueryExtensions {

		protected static function initializeScope() {

			if (!static::$_metaStore->scope->exists(static::class)) {
				static::$_metaStore->scope[static::class] = new Scope(Relation::class, static::class, static::class);
			}

		}

		static function getOrAddScope() {

			static::initializeScope();
			return static::getScope();

		}

		protected static function getScope() {
			return static::$_metaStore->scope[static::class];
		}

		protected static function hasScope($name) {

			static::staticInitialize();
			static::initializeScope();

			return static::getScope()->has($name);

		}

		protected static function scope($name, $callback = null) {

			static::initializeScope();
			static::getScope()->add($name, $callback);

		}

		protected static function tryScope($name, $args = []) {

			if (static::$_metaStore->scope[static::class]->has($name)) {

				static::$_metaStore->scope[static::class]->restart();
				return static::getScope()->$name($args);

			}

		}

	}

?>