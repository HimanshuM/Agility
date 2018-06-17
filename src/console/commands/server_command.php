<?php

namespace Agility\Console\Commands;

use Agility\Console\Helpers\ArgumentsHelper;
use Agility\Configuration;

	class ServerCommand extends Base {

		function parseOptions($args) {

			$options = ArgumentsHelper::parseOptions($args);

			$environment = getenv("AGILITY_ENV") ?: $options["e"] ?? $options["environment"] ?? "development";
			$host = $options["h"] ?? $options["host"] ?? "localhost";
			$port = $options["p"] ?? $options["port"] ?? "8000";

			$this->_environment = $environment;

			Configuration::initialize($this->_appRoot, $environment, $host, $port);

		}

		private function exportOptions() {

			putenv("AGILITY_ENV", Configuration::environment());
			putenv("AGILITY_HOST", Configuration::host());
			putenv("AGILITY_PORT", Configuration::port());

		}

		private function loadApplication($args) {

			$this->parseOptions($args);

			require_once $this->_appPath->path;
			$className = $this->_appName."\\Application";
			if (!class_exists($className)) {

				echo "Could not locate class '$className'. Has the namespace or class name in config/application.php been edited?";
				return;

			}

			return $className;

		}

		function perform($args) {

			if (!$this->requireApp()) {
				return;
			}

			$className = $this->loadApplication($args);

			if ($this->_environment == "production") {

				$this->exportOptions();

			}
			else {

				$app = new $className;
				$app->run();

			}

		}

	}

?>