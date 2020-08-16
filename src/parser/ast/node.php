<?php

namespace Agility\Parser\Ast;

	class Node {

		protected $token;
		protected $left = false;
		protected $right = false;
		protected $parent = false;

		function __construct($token) {
			$this->token = $token;
		}

		function left($node = false) {

			if ($node === false) {
				return $this->left;
			}

			$this->left = $node;

		}

		function right($node = false) {

			if ($node === false) {
				return $this->right;
			}

			$this->right = $node;

		}

		function parent($node = false) {

			if ($node === false) {
				return $this->parent;
			}

			$this->parent = $node;

		}

		function token() {
			return $this->token;
		}

	}

?>