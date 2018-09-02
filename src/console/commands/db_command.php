<?php

namespace Agility\Console\Commands;

use Agility\Data\Connection\Pool;
use Agility\Data\Migration\Runner;
use Agility\Initializers\ApplicationInitializer;
use ArrayUtils\Arrays;
use StringHelpers\Str;

	class DbCommand extends Base {

		use ApplicationInitializer;

		protected $migrationRunner;

		function __construct() {

			parent::__construct();
			$this->migrationRunner = new Runner($this->appPath->parent->chdir("../db/migrate"));

		}

		function drop($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);

		}

		function migrate($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);
			$count = $this->migrationRunner->executePendingMigrations();
			if ($count == 0) {
				echo "Nothing to migrate.";
			}
			else {
				echo "$count migration".($count > 1 ? "s" : "")." processed.";
			}

		}

		function reset($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($this->_appPath, $this->_appName, $args);

			$this->drop($args);
			$this->migrate($args);

		}

		function seed($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);

		}

	}

?>