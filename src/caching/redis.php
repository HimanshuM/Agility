<?php

namespace Agility\Caching;

use Agility\Configuration;
use ArrayUtils\Arrays;
use FileSystem\File;
use Predis\Client;
use Throwable;

	final class Redis {

		private static $defaultConnection = 0;
		private static $pool;

		private static $isInitialized = false;

		static function connection($connectionName = null) {

			Redis::initialize();

			if (empty($connectionName)) {
				$connectionName = Redis::$defaultConnection;
			}

			if (!Redis::$pool->exists($connectionName)) {
				throw new ConnectionNotFoundException($connectionName);
			}

			return Redis::$pool[$connectionName];

		}

		protected static function initialize() {

			if (Redis::initialized()) {
				return;
			}

			Redis::$pool = new Arrays;
			return Redis::parseConfiguration();

		}

		protected static function initialized() {
			return Redis::$isInitialized;
		}

		protected static function parseConfiguration() {

			$configuration = Redis::readConfigurationFile();
			$configuration = Configuration::redisConf(json_decode($configuration, true))[Configuration::environment()];
			foreach ($configuration as $connectionName => $connectionArray) {

				Redis::$pool[$connectionName] = false;
				if (is_null($connectionObject = Redis::attemptConnection($connectionArray))) {
					throw new SqlConnectionFailedException("Could not connect to redis '".$connectionArray["database"]."'");
				}

				Redis::$pool[$connectionName] = $connectionObject;

			}

			Redis::$isInitialized = true;

		}

		protected static function readConfigurationFile() {

			if (($file = Configuration::documentRoot()->has("config/redis.json")) === false) {
				return false;
			}

			return File::open($file)->read();

		}

		protected static function attemptConnection($connection) {
			return new Client($connection);
		}

		static function __callStatic($method, $args = []) {
			return Redis::connection()->$method(...$args);
		}

		static function subscribe($channel, $callback) {

			$pubSub = Redis::connection()->pubSubLoop();
			$pubSub->subscribe($channel);

			try {
				Redis::subscribeUsing($pubSub, $callback);
			}
			catch (Throwable $e) {
				Redis::subscibe($channel, $callback);
			}

		}

		protected static function subscribeUsing($pubSub, $callback) {

			foreach ($pubSub as $message) {

				if ($message->kind == "message") {
					call_user_func_array($callback, [$message->payload]);
				}

			}

		}

	}

?>