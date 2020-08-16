<?php

namespace Agility\Parser;

	class Token {

		const KEYWORDS = [
			"array", "class", "function", "private", "protected", "public"
		];

		const OPERATORS = [
			"=", "+", "-", "*", "/", "?", ":", "!", "&", "|", "%", ">", "<", "."
		];

		const COMPARISON_OPERATORS = [
			"==", "&&", "||", ">", "<", ">=", "<=", "!=", "===", "!=="
		];

		const ASSOCIATION_OPERATORS = [
			"->", "::", "=>"
		];

		const SEPARATORS = [
			",", ";"
		];

		const GROUPERS = [
			"[" => "]",
			"(" => ")",
			"{" => "}"
		];

		public $token;
		public $type;
		public $children = [];

		function __construct($token, $type) {

			$this->token = $token;
			$this->type = $type;

		}

		function type() {
			return $this->type;
		}

		static function integer($token) {
			return new Token($token, "integer");
		}

		static function float($token) {
			return new Token($token, "double");
		}

		static function word($token) {

			if (in_array($token, Token::KEYWORDS)) {
				return new Token($token, "keyword");
			}

			return new Token($token, "symbol");

		}

		static function operator($token) {
			return new Token($token, "operator");
		}

		static function string($token) {
			return new Token($token, "string");
		}

		static function separator($token) {
			return new Token($token, "separator");
		}

		static function grouper($token) {
			return new Token($token, "grouper");
		}

		static function isOperator($token) {
			return in_array($token, Token::OPERATORS);
		}

		static function isComparison($first, $second) {
			return in_array($first.$second, TOKEN::COMPARISON_OPERATORS);
		}

		static function isAssociation($first, $second) {
			return in_array($first.$second, Token::ASSOCIATION_OPERATORS);
		}

		static function isSeparator($token) {
			return in_array($token, Token::SEPARATORS);
		}

		static function grouperBegins($token) {
			return Token::GROUPERS[$token] ?? false;
		}

		static function grouperEnds($token) {
			return in_array($token, Token::GROUPERS);
		}

	}

?>