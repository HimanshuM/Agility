<?php

namespace Agility;

use ArrayUtils\Arrays;
use FileSystem\FileSystem;
use ReflectionClass;
use StringHelpers\Str;
use Swoole;

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

		static function setupApplicationAutoloader($cwd) {

			spl_autoload_register(function($class) use ($cwd) {

				$components = new Arrays(explode("\\", $class));
				$class = $components->map(function($each) {
					return Str::snakeCase(lcfirst($each));
				})->implode("/");
				if (file_exists($class.".php")) {
					require_once($class.".php");
				}

			});

		}

		protected static function tryLoadingModel($modelFile) {

			$modelClass = substr($modelFile, strpos($modelFile, "app/models/") + strlen("app/models/"), -4);
			$modelClass = "App\\Models\\".Str::normalize($modelClass);
			if (class_exists($modelClass)) {

				$classInfo = new ReflectionClass($modelClass);
				if (!$classInfo->isAbstract() && method_exists($modelClass, "staticInitialize") && is_subclass_of($modelClass, Data\Model::class)) {
					$modelClass::staticInitialize();
				}

			}

		}

	}

?>