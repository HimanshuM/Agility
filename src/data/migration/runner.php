<?php

namespace Agility\Data\Migration;

use Agility\Data\Exceptions\SqlException;
use ArrayUtils\Arrays;
use Exception;

	class Runner {

		protected $migrationsDir;
		protected $allMigrations;

		function __construct($migrationsDir) {

			$this->migrationsDir = $migrationsDir;
			$this->allMigrations = new Arrays;

		}

		protected function diffMigrations($previousMigrations) {
			return $this->allMigrations->map(":version")->diff($previousMigrations);
		}

		protected function executeMigration($migration) {

			$class = "\\Db\\Migrate\\".$migration->className;
			require_once $this->migrationsDir."/".$migration->fileName.".php";
			if (class_exists($class)) {

				try {

					$migration = new $class;
					$migration->processMigration();

				}
				catch (SqlException $e) {
					die($e."\n");
				}
				catch (Exception $e) {
					die($e->getMessage()."\n");
				}

			}

		}

		protected function executeMigrations($versions) {

			foreach ($versions as $version) {
				$this->executeMigration($this->allMigrations[$version]);
			}

		}

		function executePendingMigrations() {

			if (empty($pending = $this->needsMigration())) {
				return 0;
			}

			return $this->executeMigrations($pending);

		}

		protected function listMigrations() {

			$allMigrations = $this->migrationsDir->children;
			foreach ($allMigrations as $migrationFile) {

				$migration = $this->migration($migrationFile);
				$this->allMigrations[$migration->version] = $migration;

			}

		}

		protected function migration($migrationFile) {
			return SchemaMigration::prepare($migrationFile->name);
		}

		protected function needsMigration() {

			$this->listMigrations();

			try {
				$previousMigrations = SchemaMigration::all();
			}
			catch (Exception $e) {

				SchemaMigration::createTable();
				$previousMigrations = new Arrays;

			}

			return $this->diffMigrations($previousMigrations);

		}

		protected function runMigration() {

		}

	}

?>