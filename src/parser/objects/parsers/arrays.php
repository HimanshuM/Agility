<?php

namespace Agility\Parser\Objects\Parsers;

use Agility\Parser\Objects\Arrays as ArrayObject;
use Agility\Parser\SymbolTable;

	class Arrays {

		protected $tokens = [];
		protected $tempArray = [];
		protected $i = 0;
		protected $position = 0;

		function __construct($token) {

			$this->token = $token;
			$this->tokens = $token->children;

		}

		static function parse($token) {

			$parser = new Arrays($token);
			return $parser->consume();

		}

		protected function consume() {

			while ($this->position < count($this->tokens)) {
				$this->parseToken();
			}

			$object = new ArrayObject($this->tempArray);
			$object->token = $this->token;

			return $object;

		}

		protected function parseToken() {

			if ($this->i == 0) {

				$this->tempArray[] = $this->validateElement();
				$this->position++;

			}
			elseif ($this->i == 1) {
				$this->validateAssociation();
			}
			else {

				$this->skipSeparator();
				$this->i = -1;

			}

			$this->i++;

		}

		protected function peekNext() {
			return $this->tokens[$this->position]->token == "=>";
		}

		protected function validateElement() {

			$token = $this->tokens[$this->position];
			if (in_array($token->type, ["string", "integer", "double"])) {
				return $token->token;
			}
			elseif ($token->type == "symbol") {

				$value = SymbolTable::get($token);
				if ($value === false) {
					throw new \Exception("Use of uninitialized variable ".$token->token);
				}

				return $value;

			}
			elseif ($token->token == "[]") {
				return Arrays::parse($token);
			}
			elseif (is_scalar($token)) {
				return $token;
			}
			else {
				throw new \Exception("Unexpected ".$token->token);
			}

		}

		protected function validateAssociation() {

			if ($this->peekNext()) {

				$last = array_pop($this->tempArray);
				$this->position++;
				$this->tempArray[$last] = $this->validateElement();
				$this->position++;

			}

		}

		protected function skipSeparator() {

			$token = $this->tokens[$this->position]->token;
			if ($token == "[]") {
				return;
			}
			if ($token != ",") {
				throw new \Exception("Unexpected ".$token);
			}

			$this->position++;

		}

	}

?>