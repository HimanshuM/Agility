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

			if (defined("static::ExportableAttributes")) {

				$exportableAttributes = static::ExportableAttributes;

				return $this->attributes->toArray->filter(function($name) use ($exportableAttributes) {
					return in_array($name, $exportableAttributes);
				}, ARRAY_FILTER_USE_KEY);

			}

			return $this->attributes;

		}

		function toArray() {
			return json_decode($this->toJson, true);
		}

		function toJson() {
			return json_encode($this->jsonSerialize());
		}

	}

?>