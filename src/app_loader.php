<?php

namespace Agility;

use Swoole;
use FileSystem\FileSystem;
use StringHelpers\Str;

	class AppLoader {

		static function executeApp($argv) {

			$root = FileSystem::path("/");

			$cwd = FileSystem::cwd();
			while ($cwd != $root || $cwd->has("bin/agility") !== false) {

				if ($cwd->has("bin/agility")) {

					$appPath = $cwd->cwd."/bin/agility";
					// Swoole\Event::defer(function() use ($appPath, $argv) {
					passthru("exec $appPath ".$argv->implode(" "));
					// });
					die;

				}

				$cwd->chdir("..");

			}

			return false;

		}

		protected static function iterateThroughModels($children) {

			foreach ($children as $model) {

				if ($model->isFile) {
					AppLoader::tryLoadingModel($model);
				}
				else {
					AppLoader::iterateThroughModels($model->children);
				}

			}

		}

		static function loadModels() {

			$models = Configuration::documentRoot()->children("app/models");
			AppLoader::iterateThroughModels($models);

		}

		protected static function tryLoadingModel($modelFile) {

			$modelClass = "App\\Models\\".Str::camelCase($modelFile->name);
			if (class_exists($modelClass)) {

				if (method_exists($modelClass, "staticInitialize")) {
					$modelClass::staticInitialize();
				}

			}

		}

	}

?>