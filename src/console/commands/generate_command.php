<?php

namespace Agility\Console\Commands;

use Agility\Data\Generators\MigrationGenerator;
use Agility\Data\Generators\ModelGenerator;
use Agility\Generators\ControllerGenerator;
use Agility\Generators\ScaffoldGenerator;
use ArrayUtils\Arrays;
use FileSystem\Dir;
use FileSystem\File;
use StringHelpers\Inflect;
use StringHelpers\Str;

	class GenerateCommand extends Base {

		private $_quite = false;
		private $_overwrite = false;
		private $_options;

		const Shorthand = [
			"c" => "controller",
			"m" => "model",
			"s" => "scaffold"
		];

		private function _controller($args) {
			ControllerGenerator::start($this->_appRoot, $args);
		}

		private function _echo($str) {

			if (!$this->_quite) {
				Cli::echo($str."\n");
			}

		}

		private function _invalidCommand($stub) {

			echo "Invalid stub '$stub'.\n";
			$this->_invokeHelp("generate/help");

		}

		private function _invokeHelp($help) {

			$help = new HelpCommand;
			$help->perform($help);

		}

		private function _migration($args) {
			MigrationGenerator::start($this->_appRoot, $args);
		}

		private function _model($args) {
			ModelGenerator::start($this->_appRoot, $args);
		}

		function perform($args) {

			if (!$this->requireApp()) {
				return;
			}

			// $this->_parseOptions($args);

			$stub = $args->shift();
			$stub = strtolower($stub);

			if (isset(GenerateCommand::Shorthand[$stub])) {
				$stub = GenerateCommand::Shorthand[$stub];
			}

			if (in_array($stub, ["controller", "migration", "model", "scaffold", "task"])) {

				if ($this->requireArgs($args, $stub)) {

					$stub = "_".$stub;
					$this->$stub($args);

				}

			}
			else {
				$this->_invalidCommand($stub);
			}

		}

		private function requireArgs($args, $help) {

			if ($args->empty) {

				$this->_invokeHelp($help);
				return false;

			}

			return true;

		}

		private function _scaffold($args) {

			ControllerGenerator::start($this->_appRoot, $args, true);
			ModelGenerator::start($this->_appRoot, $args);

		}

		private function _task($args) {

		}

	}

?>