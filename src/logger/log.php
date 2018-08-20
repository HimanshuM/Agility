<?php

namespace Agility\Logger;

use Agility\Configuration;

	class Log {

		protected $logDir;
		private static $loggerName;
		private static $instance;

		protected function __construct() {
			$this->logDir = Configuration::logPath();
		}

		static function error() {
			call_user_func_array([Log::$instance, "log"], func_get_args());
		}

		static function initialize() {

			if (empty(Log::$instance)) {

				if (empty(Log::$loggerName)) {
					Log::$loggerName = "Agility\\Logger\\Log";
				}

				$loggerName = Log::$loggerName;

				Log::$instance = new $loggerName;

			}

		}

		protected function log() {

		}

		static function info() {
			call_user_func_array([Log::$instance, "log"], func_get_args());
		}

		static function notice() {
			call_user_func_array([Log::$instance, "log"], func_get_args());
		}

		static function register($className) {
			static::$loggerName = $className;
		}

		static function warn() {
			call_user_func_array([Log::$instance, "log"], func_get_args());
		}

	}

?>