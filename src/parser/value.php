<?php

namespace Agility\Parser;

	class Value {

		protected $type = false;
		protected $value = false;
		protected $token;

		function __construct($token) {

			$this->token = $token;
			if (in_array($token->type, ["string", "int", "float"])) {

				$this->value = $token->value;
				$this->type = $token->type;

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

			if ($this->isUninitialized()) {
				throw new \Exception("Use of uninitialized variable ".$this->token->token);
			}

			return $this->value;

		}

		function type() {
			return $this->type;
		}

		function token() {
			return $this->token;
		}

	}