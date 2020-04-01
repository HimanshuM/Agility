<?php

namespace Agility\Console\Commands;

use Agility\Initializers\ApplicationInitializer;
use Agility\Parser\Tokenizer;

	class ConsoleCommand extends Base {

		use ApplicationInitializer;

		function perform($args) {

			if (!$this->requireApp()) {
				return;
			}

			$this->initializeApplication($args);
			$this->repl();

		}

		protected function repl() {

			$input = $this->prompt();
			if ($input === false) {

				echo "\nBye!\n";
				return;

			}

			readline_add_history($input);
			try {
				var_dump(Tokenizer::parse($input));
			}
			catch (\Exception $e) {
				echo $e->getMessage()."\n";
			}

			$this->repl();

		}

		protected function prompt() {

			echo "agility:> ";
			return readline();

		}

	}

?>