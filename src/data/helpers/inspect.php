<?php

namespace Agility\Data\Helpers;

	trait Inspect {

		function __debugInfo() {
			return $this->attributes->toArray->all;
		}

		static function inspect() {

			static::generateAttributes();

			$attributes = [];
			foreach (static::generatedAttributes() as $name => $attribute) {
				$attributes[] = $name.": ".$attribute->dataType;
			}

			return static::class." (".implode(", ", $attributes).")";

		}

		function jsonSerialize() {
			return $this->attributes;
		}

	}

?>