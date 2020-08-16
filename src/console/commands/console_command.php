<?php

namespace Agility\Console\Commands;

use Agility\Console\Helpers\EchoHelper;
use Agility\Console\Helpers\OutputHelper;
use Agility\Initializers\ApplicationInitializer;
use Agility\Parser\Lexer;
use Agility\Parser\SymbolTable;
use Agility\Parser\Tokenizer;

	class ConsoleCommand extends Base {

		use ApplicationInitializer;
		use EchoHelper;

		protected $quite = false;

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

				$tokens = Tokenizer::parse($input);
				$this->echo("#B#=>#N# ".Lexer::use($tokens));

			}
			catch (\Exception $e) {
				$this->echo("#Red#".$e->getMessage()."#N#");
			}

			$this->repl();

		}

		protected function prompt() {

			OutputHelper::echo("#B##White#agility:>#N# ");
			return readline();

		}

	}

?>