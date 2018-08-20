<?php

namespace Agility\Data\Persistence;

	trait State {

		private $_fresh;
		private $_dirty = false;
		private $_destroyed = false;
		private $_persisted = false;

		function destroyed() {
			return $this->_destroyed;
		}

		function dirty() {
			return $this->_dirty;
		}

		function fresh() {
			return $this->_fresh;
		}

		function persisted() {
			return $this->_persisted;
		}

	}

?>