<?php

namespace Agility\Data\Persistence;

use Agility\Data\Relation;

	trait Persist {

		static function create() {

			$obj = forward_static_call_array([static::class, "new"], func_get_args());
			$obj->save();

			return $obj;

		}

		private function _createNew() {

			$this->_runCallbacks("beforeCreate");

			$attributes = $this->fetchAttributes(false);

			$relation = new Relation(static::class, Relation::Insert);
			if (($id = $relation->insert($attributes)->execute) == 0) {
				return false;
			}
			$this->_setAttribute(static::$primaryKey, $id);

			$this->_runCallbacks("afterCreate");
			return true;

		}

		private function createOrUpdate() {

			$this->_performValidations($this->_fresh);
			$this->_runCallbacks("beforeSave");

			if ($this->_fresh) {
				$result = $this->_createNew();
			}
			else {
				$result = $this->_update();
			}

			$this->_runCallbacks("afterSave");
			return $result;

		}

		private function delete() {

			$this->_runCallbacks("beforeDelete");

			$relation = new Relation(static::class, Relation::Delete);
			if ($relation->delete([static::$primaryKey => $this->_getAttribute(static::$primaryKey)])->execute == 0) {
				return false;
			}

			$this->_runCallbacks("afterDelete");
			return true;

		}

		static function new() {

			$obj = new static;

			$args = func_get_args();
			if (count($args) > 0) {

				if (is_array($args[0])) {
					$obj->fillAttributes($args);
				}
				else if (is_callable($args[0])) {
					$args[0]($obj);
				}

			}

			return $obj;

		}

		function save() {
			return $this->createOrUpdate();
		}

		private function _update() {

			$this->_runCallbacks("beforeUpdate");

			$attributes = $this->fetchAttributes(false);
			$primaryKey = $attributes[static::$primaryKey];
			unset($attributes[static::$primaryKey]);

			$relation = new Relation(static::class, Relation::Update);
			if ($relation->update($attributes)->where([static::$primaryKey => $primaryKey])->execute() == 0) {
				return false;
			}

			$this->_runCallbacks("afterUpdate");
			return true;

		}

	}

?>