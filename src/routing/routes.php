<?php

namespace Agility\Routing;

use Agility\Configuration;

	class Routes {

		private static $ast;

		static function ast() {

			if (empty(Routes::$ast)) {
				Routes::$ast = new MethodTrees;
			}

			return Routes::$ast;

		}

		static function draw($callback) {
			Routes::invokeCallback("\\App\\Controllers\\", $callback);
		}

		static function initialize() {

			if (($file = Configuration::documentRoot()->has("config/routes.php")) === false) {
				throw new Exceptions\RoutesNotFoundException;
			}

			require_once $file;
			return true;

		}

		static function inspect($verb = false) {

			$verb = $verb ? [$verb] : ["get", "post", "put", "patch", "delete"];
			foreach ($verb as $method) {
				var_dump(Routes::$ast->$method);
			}

		}

		static function invokeCallback($rootNamespace, $callback) {

			Routes::ast();
			$builder = new Builder($rootNamespace, Routes::$ast);
			($callback->bindTo($builder, $builder))();

		}

	}

?>