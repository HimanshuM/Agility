<?php

namespace Agility\Initializers;

use Agility\Configuration;
use StringHelpers\Str;

	class PreInitializer {

		static $initializers = [
			"data_initializer",
		];

		static function execute() {

			foreach (PreInitializer::$initializers as $initializer) {

				if (Configuration::documentRoot()->has("/config/initializers/$initializer.php")) {

					require_once(Configuration::documentRoot()->has("/config/initializers/$initializer.php"));
					$className = Str::camelCase($initializer);
					(new $className)->configure();

				}

			}

		}

	}

?>