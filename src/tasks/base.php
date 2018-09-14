<?php

namespace Agility\Tasks;

use Agility\Console\Command;
use Agility\Console\Helpers\ArgumentsHelper;
use Agility\Console\Helpers\EchoHelper;

	class Base {

		use EchoHelper;

		protected $quite;

		function __construct() {

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

	}

?>