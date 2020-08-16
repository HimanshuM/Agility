<?php

namespace Agility\Parser;

	class Lexer {

		protected $tokens = [];
		protected $ast;

		protected function __construct($tokens) {

			$this->tokens = $tokens;
			$this->ast = new Ast\Tree;

		}

		static function use($tokens) {

			$lexer = new Lexer($tokens);
			$lexer->consume();
			return $lexer->compile();

		}

		protected function consume() {

			foreach ($this->tokens as $token) {
				$this->parse($token);
			}

		}

		protected function parse($token) {

			if ($token->type == "symbol") {
				SymbolTable::insertIfNot($token);
			}
			elseif ($token->type == "grouper") {

				if ($token->token == "[]") {
					$token = Objects\Parsers\Arrays::parse($token);
				}

			}

			$this->ast->add($token);

		}

		function print() {
			$this->ast->print();
		}

		function compile() {
			return $this->ast->walk();
		}

	}

?>