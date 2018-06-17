<?php

namespace Agility\Data\Connection;

	class AbstractType {

		function getNativeType($type, $limit = null, $precision = null, $scale = null) {

			if (!empty(static::NativeTypes[$type])) {

				if ($type == "float" || $type == "decimal") {

					$precision = $precision ?: static::NativeTypes[$type]["precision"];
					$scale = $scale ?: static::NativeTypes[$type]["scale"];

					return $type."($precision, $scale)";

				}

				if ($type == "datetime" || $type == "timestamp") {
					return $type.($precision ? "($precision)" : "");
				}

			}

			return $type;

		}

	}

?>