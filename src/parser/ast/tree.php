<?php

namespace Agility\Parser\Ast;

use Agility\Parser\Operator;
use Agility\Parser\SymbolTable;
use Agility\Parser\Value;
use Agility\Parser\Objects\Type;

	class Tree {

		protected $head = false;
		protected $current = false;

		function add($token) {

			if ($this->head == false) {
				$this->current = $this->head = new Node($token);
			}
			elseif ($token->type == "operator") {
				$this->moveUsing($token);
			}
			else {
				$this->append($token);
			}

		}

		protected function moveUsing($token) {

			if ($token->type != "operator") {
				throw new \Exception("Unexpected ".$token->token);
			}

			$moveHead = false;
			if ($this->current == $this->head) {
				$moveHead = true;
			}

			$temp = $this->current;
			$this->current = new Node($token);
			if ($moveHead) {
				$this->head = $this->current;
			}
			elseif ($temp->parent() !== false) {
				$temp->parent()->right($this->current);
			}

			$temp->parent($this->current);
			$this->current->left($temp);

		}

		protected function append($token) {

			$node = new Node($token);
			$node->parent($this->current);

			if ($this->current->token()->type == "operator" && $this->current->left() == false) {
				$this->current->left($node);
			}
			else {

				$this->current->right($node);
				$this->current = $this->current->right();

			}

		}

		function print() {
			$this->printRecursive($this->head);
		}

		protected function printRecursive($node, $left = false, $depth = 0) {

			if ($node === false) {
				return;
			}

			$this->printDepth($left, $depth);
			echo $node->token()->token."\n";
			$this->printRecursive($node->left(), true, $depth + 1);
			$this->printRecursive($node->right(), false, $depth + 1);

		}

		protected function printDepth($left, $depth) {

			if ($depth == 0) {
				return;
			}

			echo $left ? "LEFT:" : "RIGHT:";
			for ($i = 0; $i < $depth; $i++) {
				echo "-";
			}

		}

		function walk() {

			$value = $this->traverse($this->head);
			if ($value instanceof Value) {
				return $value->get();
			}

			return $value;

		}

		protected function traverse($node, $using = null) {

			if ($node === false) {
				return;
			}

			if ($node->token()->type == "operator") {

				$lhs = $this->traverse($node->left());
				$rhs = $this->traverse($node->right());

				return Operator::perform($node->token()->token, $lhs, $rhs);

			}

			$value = $this->evaluate($node->token());
			if (!is_null($using)) {

				$token = $node->token();
				if (is_a($token, Type::class)) {
					$value = $token->resolveFor($using);
				}

			}
			if (!empty($node->right())) {
				return $this->traverse($node->right(), $value);
			}

			return $value;

		}

		protected function evaluate($token) {

			if (in_array($token->type, ["float", "int", "string"])) {
				return $token;
			}
			elseif ($token->type == "symbol") {
				return SymbolTable::getOrInsert($token);
			}

			return $token;

		}

	}

?>