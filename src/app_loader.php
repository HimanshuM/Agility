<?php

namespace Agility;

use Swoole;
use FileSystem\FileSystem;

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

	}

?>