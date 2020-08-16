<?php

namespace Agility\Tasks;

use Agility\Console\Command;
use Agility\Console\Helpers\ArgumentsHelper;
use Agility\Console\Helpers\EchoHelper;
use Agility\Initializers\ApplicationInitializer;
use FileSystem;
use StringHelpers\Str;

	class Base {

		use EchoHelper;
		use ApplicationInitializer {
			parseOptions as private initParseOption;
		}

		protected $quite;
		protected $_appName = "";
		protected $_appPath = null;
		protected $_appRoot = null;
		protected $_basePath;

		function __construct($args = []) {

			if (defined("APP_PATH")) {
				$this->_appPath = FileSystem\FileSystem::path(constant("APP_PATH"));
			}

			$this->getAppName();
			$this->initParseOption($args);
			$this->initializeApplication($args, true);

		}

		protected function ask($prompt) {
			return readline($prompt." ");
		}

		protected function parseOptions($args) {
			ArgumentsHelper::parseOptions($args, true);
		}

		protected function runTask($taskName, $args = []) {
			Command::invoke($taskName, $args);
		}

		protected function getAppName() {

			if (!empty($this->_appPath)) {

				if ($this->_appPath->isDir()) {

					$this->_appRoot = $this->_appPath;
					$this->_appName = Str::camelCase($this->_appPath->basename);

				}
				else {

					$this->_appRoot = $this->_appPath->parent;
					$appName = $this->_appRoot->chdir("..")->basename;
					$this->_appName = Str::camelCase($appName);

				}

			}

		}

	}

?>