<?php

namespace Agility\Console;

use ArrayUtils\Arrays;
use FileSystem\FileSystem;
use StringHelpers\Str;

	class Command {

		static private $_hiddenCommands = null;

		static function hidden($className) {

			if (is_null(self::$_hiddenCommands)) {
				self::$_hiddenCommands = new Arrays;
			}

			self::$_hiddenCommands[] = $className;

		}

		static function invoke($command, $args) {

			static::hidden("Agility\\Console\\Commands\\Base");

			$namespaces = [[FileSystem::path(__DIR__."/commands"), "Agility\\Console\\Commands"]];
			if (defined("APP_PATH")) {

				$appPath = FileSystem::path(APP_PATH);
				$appPath = $appPath->cwd->chdir("..");

				$namespaces[] = [$appPath->chdir("lib/tasks"), "Tasks"];

			}

			if (($result = self::lookup($command, $namespaces)) === false) {

				echo "Command '$command' not found.\n";
				return;

			}

			list($class, $method) = $result;

			$object = new $class;

			// A class present inside Commands or lib/tasks directory can register itself as hidden from it's constructor by invoking Agility\Command::hidden("<namespace\class_name>"");
			if (self::$_hiddenCommands->has($class)) {
				echo "'$command' does not exist. Please type 'agility help' to see the list of available commands.";
			}
			else {
				$object->$method($args);
			}

			echo "\n";

		}

		static function lookup($command, $namespaces) {

			if (strpos($command, ":") !== false) {
				list($class, $method) = explode(":", $command);
			}
			else {

				$class = $command;
				$method = "perform";

			}

			$classPath = $class;

			foreach ($namespaces as $namespace) {

				if ($namespace[1] == "Agility\\Console\\Commands") {
					$classPath .= "_command";
				}
				$class = Str::camelCase($classPath);

				if ($namespace[0]->has($classPath.".php") && method_exists($namespace[1]."\\".$class, $method)) {
					return [$namespace[1]."\\".$class, $method];
				}

			}

			return false;

		}

	}

?>