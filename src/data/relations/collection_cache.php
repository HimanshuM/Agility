<?php

namespace Agility\Data\Relations;

	trait CollectionCache {

		protected $cache;
		protected $_position;

		function __debugInfo() {

			$this->initializeCache();
			return $this->cache;

		}

		public function current() {
			return $this->cache[$this->_position];
		}

		protected function initializeCache() {
			$this->_executeQuery();
		}

		function jsonSerialize() {

			$this->initializeCache();
			return $this->cache;

		}

		public function key() {
			return $this->_position;
		}

		public function next() {
			++$this->_position;
		}

		public function rewind() {

			$this->initializeCache();
			$this->_position = 0;

		}

		function serialize() {

			$this->initializeCache();
			return serialize($this->cache);

		}

		function unserialize($serialized) {

		}

		public function valid() {

			$this->initializeCache();
			return $this->cache->exists($this->_position);

		}

	}

?>