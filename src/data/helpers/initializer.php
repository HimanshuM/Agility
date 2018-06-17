<?php

namespace Agility\Data\Helpers;

use Agility\Data\Cache\MetaStore;
use Agility\Data\Relation;
use Agility\Data\Collection;
use Agility\Data\Connection;
use Agility\Data\Validations\Validations;
use Aqua\Table;
use ArrayUtils\Arrays;

	trait Initializer {

		protected static $_metaStore;

		static function aquaTable() {

			if (!static::$_metaStore->aquaTables->exists(static::class)) {
				static::$_metaStore->aquaTables[static::class] = new Table(static::connection()->prefix.static::tableName().static::connection()->suffix);
			}

			return static::$_metaStore->aquaTables[static::class];

		}

		static function connection() {

			if (!static::$_metaStore->connections->exists(static::class)) {

				if (empty(static::$connection)) {
					static::$_metaStore->connections[static::class] = Connection\Pool::getConnection();
				}
				else {
					static::$_metaStore->connections[static::class] = Connection\Pool::getConnection(static::$connection);
				}

			}

			return static::$_metaStore->connections[static::class];

		}

		protected function _initialize() {

			static::staticInitialize();

			$this->attributes = new Collection;
			$this->_fresh = true;
			$this->_dirty = false;

			$this->methodsAsProperties();
			$this->notFoundResponse(ACCESSOR_NOT_FOUND_CALLBACK, "defaultCallback");

			$this->_runCallbacks("afterInitialize");

		}

		protected static function initializeRelation() {

			static::staticInitialize();
			return (new Relation(static::class));

		}

		static function staticInitialize() {

			if (empty(static::$_metaStore)) {
				static::$_metaStore = MetaStore::instance();
			}

			static::tableName();
			static::connection();
			static::aquaTable();

			static::generateAttributes();
			static::initializeAssociations();

			if (!static::$_metaStore->modelInitialized->exists(static::class)) {

				if (method_exists(static::class, "initialize")) {
					static::initialize();
				}
				static::$_metaStore->modelInitialized[static::class] = true;

			}

		}

		static function tableName() {

			if (!static::$_metaStore->tableNames->exists(static::class)) {

				static::$_metaStore->tableNames[static::class] = NameHelper::tablize(static::class);
				static::connection();
				static::aquaTable();

			}

			return static::$_metaStore->tableNames[static::class];

		}

	}

?>