<?php

namespace Agility\Data\Associations;

use Agility\Data\Cache\AssociationStore;
use Agility\Data\Helpers\NameHelper;
use ArrayUtils\Arrays;
use Exception;
use StringHelpers\Inflect;
use StringHelpers\Str;

	trait Builder {

		protected static $_collectionsCache;

		protected static function belongsTo($associationName, $options = []) {

			$polymorphic = $options["polymorphic"] ?? false;

			$className = $associationName;
			if ($polymorphic === false) {

				if (!empty($options["className"])) {
					$className = $options["className"];
				}
				else {
					$className = NameHelper::classify($associationName, static::class, false);
				}

				if (!class_exists($className)) {
					throw new Exception("Could not find class '$className'.", 1);
				}

				$foreignKey = $options["foreignKey"] ?? Inflect::singularize($className::tableName())."Id";
				$primaryKey = $options["primaryKey"] ?? $className::$primaryKey;

			}
			else {

				$foreignKey = $className."Id";
				$primaryKey = "id";

			}


			static::belongsToAssociations()[$associationName] = new ParentAssociation($foreignKey, $className, $primaryKey, $polymorphic);

		}

		protected static function belongsToAssociations() {

			if (!static::$_collectionsCache->belongsToAssociations->exists(static::class)) {
				static::$_collectionsCache->belongsToAssociations[static::class] = new Arrays;
			}

			return static::$_collectionsCache->belongsToAssociations[static::class];

		}

		protected static function hasMany($associationName, $options = [], $callback = null) {

			$precedent = "className";
			$subsequent = "source";

			$through = $options["through"] ?? null;

			$as = false;
			$sourceType = false;
			if (!empty($options["as"])) {

				$as = $options["as"];
				$options["foreignKey"] = $options["foreignKey"] ?? $as."_id";

			}
			else if (!empty($through)) {

				if (!static::hasManyAssociations()->exists($through)) {
					throw new Exceptions\HasManyThroughNotFoundException($through, static::class);
				}

				$through = static::hasManyAssociations()[$through];

				$precedent = "source";
				$subsequent = "className";

			}

			$associatedClass = null;
			$associatedName = $associationName;
			if (!empty($options[$precedent])) {

				$associatedClass = NameHelper::classify(Str::camelCase($options[$precedent]), static::class, true);
				$associatedName = $options[$precedent];

			}
			else if (!empty($options[$subsequent])) {

				$associatedClass = NameHelper::classify(Str::camelCase($options[$subsequent]), static::class, true);
				$associatedName = $options[$subsequent];

			}
			else {
				$associatedClass = NameHelper::classify($associationName, static::class);
			}

			if (!class_exists($associatedClass)) {
				throw new Exception("Could not find class '$associatedClass'.", 1);
			}

			$primaryKey = $options["primaryKey"] ?? static::$primaryKey;
			$foreignKey = $options["foreignKey"] ?? Inflect::singularize(static::tableName())."_id";

			$sourceType = $options["sourceType"] ?? str_replace("App\\Models\\", "", $associatedClass);

			static::hasManyAssociations()[$associationName] = new DependentAssociation($associationName, static::class, $primaryKey, $associatedClass, $associatedName, $foreignKey, $through, $as, $sourceType, $callback);

		}

		protected static function hasManyAssociations() {

			if (!static::$_collectionsCache->hasManyAssociations->exists(static::class)) {
				static::$_collectionsCache->hasManyAssociations[static::class] = new Arrays;
			}

			return static::$_collectionsCache->hasManyAssociations[static::class];

		}

		protected static function hasAndBelongsToMany() {

		}

		protected static function hasAndBelongsToManyAssociations() {

			if (!static::$_collectionsCache->hasAndBelongsToManyAssociations->exists(static::class)) {
				static::$_collectionsCache->hasAndBelongsToManyAssociations[static::class] = new Arrays;
			}

			return static::$_collectionsCache->hasAndBelongsToManyAssociations[static::class];

		}

		protected static function hasOne() {

		}

		protected static function hasOneAssociations() {

			if (!static::$_collectionsCache->hasOneAssociations->exists(static::class)) {
				static::$_collectionsCache->hasOneAssociations[static::class] = new Arrays;
			}

			return static::$_collectionsCache->hasOneAssociations[static::class];

		}

		protected static function initializeAssociations() {

			if (empty(static::$_collectionsCache)) {
				static::$_collectionsCache = AssociationStore::instance();
			}

		}

	}

?>