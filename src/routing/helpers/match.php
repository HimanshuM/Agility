<?php

namespace Agility\Routing\Helpers;

	trait Match {

		function match($verbs, $path, $handler, $options = []) {

			$verbs = array_intersect(["delete", "get", "head", "options", "patch", "post", "put"], $verbs);
			foreach ($verbs as $verb) {
				$this->constructRoute($verb, $path, $handler, $options);
			}

		}

		function root($handler) {
			$this->constructRoute("get", "/", $handler, []);
		}

	}

?>