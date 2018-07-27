<?php

namespace Agility\Console\Commands;

use Agility\Data\Connection\Pool;
use Agility\Initializers\ApplicationInitializer;
use ArrayUtils\Arrays;
use StringHelpers\Str;

	class DbCommand extends Base {

		use ApplicationInitializer;

		function drop($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);

		}

		protected function initializeApplication($args) {

			$className = $this->loadApplication($args);

			$app = new $className;
			$app->initializeComponents();

		}

		function migrate($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);

			Pool::initialize();

			$cwd = $this->appPath->parent->chdir("../db/migrate");
			$file = $cwd->children->last;
			require_once $file->path;
			$fileName = Arrays::split("_", $file->name);
			$className = "\\Db\\Migrate\\".Str::camelCase($fileName->skip(1)->join);
			if (class_exists($className)) {

				$migration = new $className;
				$migration->processMigration();

			}

		}

		function reset($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);

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