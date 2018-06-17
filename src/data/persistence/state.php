<?php

namespace Agility\Data\Persistence;

	trait State {

		private $_fresh;
		private $_dirty = false;
		private $_destroyed = false;

		function fresh() {
			return $this->_fresh;
		}

		function dirty() {
			return $this->_dirty;
		}

		function destroyed() {
			return $this->_destroyed;
		}

	}

?>