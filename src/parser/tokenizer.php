<?php

namespace Agility\Parser;

	class Tokenizer {

		const METHODS = ["consumeNumerals", "consumeWord", "consumeOperators", "consumeString", "consumeSeparators", "consumeGroups"];

		protected $string;
		protected $position = 0;
		protected $tokens = [];

		protected $grouperStart = false;
		protected $grouperEnd = false;
		protected $grouper = 0;

		protected function __construct($string) {

			$this->string = $string;
			$this->tokens = [];

		}

		static function parse($string) {

			$parser = new Tokenizer($string);
			return $parser->consume();

		}

		protected function consume($i = 0) {

			if (!$this->eol()) {

				$this->consumeWhitespace();

				$method = Tokenizer::METHODS[$i++];
				if (($token = $this->$method()) !== false) {

					if (is_array($token)) {
						$this->tokens = array_merge($this->tokens, $token);
					}
					else {
						$this->tokens[] = $token;
					}

					$i = 0;

				}
				elseif ($i >= count(Tokenizer::METHODS)) {
					throw new \Exception("Invalid character encountered: ".$this->consumeCharacter());
				}

				return $this->consume($i);

			}

			return $this->tokens;

		}

		protected function eol() {
			return $this->position >= strlen($this->string);
		}

		protected function nextChar() {

			if ($this->eol()) {
				return false;
			}

			return $this->string[$this->position];

		}

		protected function consumeCharacter() {

			if ($this->eol()) {
				return false;
			}

			return $this->string[$this->position++];

		}

		protected function consumeWhile($test) {

			$result = "";
			while (!$this->eol() && $this->test($test)) {
				$result .= $this->consumeCharacter();
			}

			return $result;

		}

		protected function test($test) {
			return call_user_func_array($test, [$this->nextChar()]);
		}

		protected function consumeWhitespace() {
			$this->consumeWhile([Identifiers::class, "isWhitespace"]);
		}

		protected function consumeNumerals() {

			if (!is_numeric($this->nextChar())) {
				return false;
			}

			$number = $this->consumeWhile("is_numeric");
			if ($this->nextChar() == ".") {

				$this->consumeCharacter();
				if (!empty($decimal = $this->consumeWhile("is_numeric"))) {
					return Token::float(floatval($number.".".$decimal));
				}
				else {

					$message = "Unexpected end of input";
					if ($this->nextChar() !== false) {
						$message = "Unexpected '".$this->consumeCharacter()."'";
					}

					throw new \Exception($message);

				}

			}

			return Token::integer(intval($number));

		}

		protected function consumeWord() {

			$char = $this->nextChar();
			if (ctype_alpha($char)) {
				return Token::word($this->consumeWhile("ctype_alnum"));
			}
			elseif ($char == "$") {
				return Token::word($this->consumeCharacter().$this->consumeWhile("ctype_alnum"));
			}

			return false;

		}

		protected function consumeOperators() {

			if (!Token::isOperator($this->nextChar())) {
				return false;
			}

			$token = $this->parseOperator();
			return Token::operator($token);

		}

		protected function parseOperator() {

			$token = $this->consumeCharacter();
			if (Token::isComparison($token, $this->nextChar())) {
				$token = $token.$this->parseOperator();
			}
			elseif (Token::isAssociation($token, $this->nextChar())) {
				$token = $token.$this->parseOperator();
			}

			return $token;

		}

		protected function consumeString() {

			if (!in_array($this->nextChar(), ["\"", "'"])) {
				return false;
			}

			$start = $this->consumeCharacter();
			$token = $this->consumeWhile(function($c) use ($start) {
				return $c != $start;
			});
			if ($this->consumeCharacter() === false) {
				throw new \Exception("Unexpected end of input. '$start' not found.");
			}

			return Token::string($token);

		}

		protected function consumeGroups() {

			if (($this->grouperEnd = Token::grouperBegins($this->nextChar())) === false) {
				return false;
			}

			$this->grouperStart = $this->consumeCharacter();
			$string = $this->consumeWhile([$this, "consumerGrouperInner"]);
			if ($this->consumeCharacter() === false) {
				throw new \Exception("Unexpected end of input. ']' not found.");
			}

			$group = Token::grouper($this->grouperStart.$this->grouperEnd);
			$group->children = Tokenizer::parse($string, false);

			$this->grouperStart = $this->grouperEnd = false;
			$this->grouper = 0;

			return $group;

		}

		protected function consumerGrouperInner($c) {

			if ($c == $this->grouperStart) {
				$this->grouper++;
			}
			if ($c == $this->grouperEnd) {
				$this->grouper--;
			}

			return $c != $this->grouperEnd || $this->grouper >= 0;

		}

		protected function consumeSeparators() {

			if (Token::isSeparator($this->nextChar())) {
				return Token::separator($this->consumeCharacter());
			}

			return false;

		}

	}

?>