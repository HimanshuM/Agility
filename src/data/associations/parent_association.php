<?php

namespace Agility\Data\Associations;

use Agility\Data\Relation;
use AttributeHelper\Accessor;

	class ParentAssociation {

		use Accessor;

		protected $_associatedForeignKey;

		protected $_ownerClass;
		protected $_primaryKey;

		protected $_polymorphic;

		protected $_relation;
		protected $_object;

		function __construct($associatedForeignKey, $ownerClass, $primaryKey, $polymorphic = false) {

			$this->_associatedForeignKey = $associatedForeignKey;

			$this->_ownerClass = $ownerClass;
			$this->_primaryKey = $primaryKey;

			$this->_polymorphic = $polymorphic;

			$this->methodsAsProperties();
			$this->prependUnderScore();
			$this->readonly("associatedForeignKey", "ownerClass", "primaryKey", "polymorphic");

		}

		function fetch($associatedObject) {

			if (!empty($this->_object)) {
				return $this->_object;
			}

			$this->prepare($associatedObject);

			$foreignKey = $this->_associatedForeignKey;
			return $this->_object = $this->_relation->where([$this->_primaryKey => $associatedObject->$foreignKey])->first;

		}

		function prepare($associatedObject = false) {

			if (empty($this->_polymorphic)) {
				$this->_relation = new Relation($this->_ownerClass);
			}
			else {

				$associatedForeignType = $this->_ownerClass."Type";
				$ownerClass = $associatedObject->$associatedForeignType;

				$this->_relation = new Relation($associatedObject->$associatedForeignType);

			}

		}

	}

?>