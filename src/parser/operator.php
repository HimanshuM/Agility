<?php

namespace Agility\Parser;

	class Operator {

		static function perform($operator, $lhs, $rhs) {


			if ($operator == "=") {

				list($lhv, $lht) = Operator::valueAndTypeOf($lhs);
				list($rhv, $rht) = Operator::valueAndTypeOf($rhs, true);

				return $lhv->set($rht, $rhv);

			}
			else {

				list($lhv, $lht) = Operator::valueAndTypeOf($lhs, true);
				list($rhv, $rht) = Operator::valueAndTypeOf($rhs, true);

				if ($operator == "+") {
					return $lhv + $rhv;
				}
				elseif ($operator == "-") {
					return $lhv - $rhv;
				}
				elseif ($operator == "*") {
					return $lhv * $rhv;
				}
				elseif ($operator == "/") {
					return $lhv / $rhv;
				}
				elseif ($operator == "%") {
					return $lhv % $rhv;
				}
				elseif ($operator == ".") {
					return $lhv.$rhv;
				}

			}

		}

		static function valueAndTypeOf($var, $eval = false) {

			if (is_a($var, Token::class)) {
				return [$var->token, $var->type];
			}
			elseif (is_scalar($var)) {
				return [$var, gettype($var)];
			}
			elseif (is_a($var, Objects\Type::class)) {
				return [$var, $var->type];
			}

			$value = $var->token();
			if ($value->type == "symbol") {

				$value = SymbolTable::get($value);
				if ($value === false) {
					throw new \Exception("Use of uninitialized variable ".$value->token);
				}

			}

			if ($eval) {
				return Operator::valueAndTypeOf($value->get());
			}

			return [$value, $value->type()];

		}

	}

?>