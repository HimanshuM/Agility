<?php

namespace Agility;

use ArrayUtils\Arrays;

	final class Configuration {

		private $_documentRoot;
		private $_environment;
		private $_host;
		private $_port;

		private $_dbConfiguration;

		private $_settings;

		private static $_instance = null;

		private function __construct($documentRoot, $environment, $host, $port) {

			$this->_documentRoot = $documentRoot;
			$this->_environment = $environment;
			$this->_host = $host;
			$this->_port = $port;

			$this->_settings = new Arrays;

		}

		static function all() {

			return static::initialized() ? new Arrays([
				"environment" => static::$_instance->_environment,
				"host" => static::$_instance->_host,
				"port" => static::$_instance->_port
			]) : new Arrays;

		}

		static function __callStatic($setting, $args = []) {

			if ($setting == "dbConfiguration") {

				if (empty(static::$_instance->_dbConfiguration) && !empty($args)) {
					static::$_instance->_dbConfiguration = $args[0];
				}

				return static::$_instance->_dbConfiguration;

			}

			if (static::$_instance->_settings->exists($setting)) {
				return static::$_instance->_settings[$setting];
			}
			else if (!empty($args)) {
				static::$_instance->_settings[$setting] = $args[0];
			}
			else {
				return null;
			}

		}

		static function documentRoot() {
			return static::initialized() ? static::$_instance->_documentRoot : null;
		}

		static function environment() {
			return static::initialized() ? static::$_instance->_environment : null;
		}

		static function initialize($documentRoot = "public", $environment = "development", $host = "localhost", $port = "8000") {

			if (is_null(static::$_instance)) {
				static::$_instance = new static($documentRoot, $environment, $host, $port);
			}

			return static::$_instance;

		}

		static function initialized() {
			return !is_null(static::$_instance);
		}

		static function host() {
			return static::initialized() ? static::$_instance->_host : null;
		}

		static function port() {
			return static::initialized() ? static::$_instance->_port : null;
		}

	}

?>