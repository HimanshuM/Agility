<?php

namespace Agility\Http\Sessions;

use Agility\Config;
use AttributeHelper\Accessor;
use Phpm\Exceptions\TypeExceptions\InvalidTypeException;

	class Configuration {

		use Accessor;

		public $cookieName = "agility_sess";
		protected $storage = "FileStore";
		public $sessionSource = "cookie";
		protected $expiry = 1200;

		protected $cookieStore = false;
		protected $databaseStore = false;
		protected $fileStore = false;

		function __construct() {

			$this->cookieStore = new CookieStore;
			$this->fileStore = new FileStore;

			$this->methodsAsProperties();

		}

		function cookieStore() {
			return $this->storage == "CookieStore" ? $this->cookieStore : false;
		}

		function databaseStore() {
			return $this->databaseStore;
		}

		function expiry($value = nil) {

			if ($value == nil) {
				return $this->expiry;
			}

			if (!is_int($value)) {
				throw new InvalidTypeException("Session store expiry", "integer");
			}

			$this->expiry = $value;

		}

		function fileStore() {
			return $this->storage == "FileStore" ? $this->fileStore : false;
		}

		function storage($value = nil) {

			if ($value == nil) {
				return $this->storage;
			}

			if ($value != "CookieStore" && $value != "FileStore") {
				$this->databaseStore = new DatabaseStore($value);
			}

			return $this->storage = $value;

		}

	}

?>