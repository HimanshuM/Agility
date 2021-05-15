<?php

namespace Agility\Http\Sessions;

use Swoole;

	abstract class BackendStore {

		abstract function cleanup();

		abstract function readSession($sessionId);

		// Set up a timer of Config::sessionStore()->expiry duration which invokes this function.
		function setupCleanup() {
			Swoole\Timer::tick(1000, [$this, "cleanup"]);
		}

		abstract function writeSession($session);

	}

?>