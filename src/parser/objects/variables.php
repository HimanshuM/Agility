<?php

namespace Agility\Parser;

	class Variables extends Type {

		protected $name;
		public $type = false;
		protected $value = false;
		protected $token;

		function __construct($token) {

			$this->token = $token;
			if (in_array($token->type, ["string", "int", "float"])) {

				$this->value = $token->value;
				$this->type = $token->type;

			}
			else {
				$this->name = $this->token->token;
			}

		}

		function isUninitialized() {
			return $this->type == false;
		}

		function set($type, $value) {

			$this->type = $type;
			return $this->value = $value;

		}

		function get() {
			return $this->value;
		}

		function type() {
			return $this->type;
		}

		function token() {
			return $this->token;
		}

	}