<?php

namespace Agility\Initializers;

use Agility\Configuration;
use StringHelpers\Str;

	final class PostInitializer {

		static $initializers = [
			"data_initializer",
		];

		static function execute() {

			foreach (PostInitializer::$initializers as $initializer) {

				if (Configuration::documentRoot()->has("/config/initializers/$initializer.php")) {

					require_once(Configuration::documentRoot()->has("/config/initializers/$initializer.php"));
					$className = Str::camelCase($initializer);
					(new $className)->configure();

				}

			}

		}

	}

?>