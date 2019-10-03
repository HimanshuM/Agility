<?php

namespace Agility\Initializers;

use Agility\Configuration;
use StringHelpers\Str;

	final class PostInitializer {

		static function execute() {

			foreach (Configuration::documentRoot()->children("config/initializers/") as $initializer) {
				require_once $initializer;
			}

			static::executeEnvironmentInitializers();

		}

		static function executeEnvironmentInitializers() {

			$envPath = "config/environments/".Configuration::environment();
			if (Configuration::documentRoot()->has($envPath)) {

				foreach (Configuration::documentRoot()->children($envPath) as $initializer) {
					require_once $initializer;
				}

			}

		}

	}

?>