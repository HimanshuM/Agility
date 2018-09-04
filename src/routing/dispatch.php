<?php

namespace Agility\Routing;

use Agility\Configuration;
use Agility\Logger\Log;
use Agility\Server\Request;
use Agility\Server\Response;
use Agility\Server\StaticContent;
use Closure;
use Exception;
use StringHelpers\Str;

	class Dispatch {

		protected $ast;
		protected $request;
		protected $response;
		protected $params;

		function __construct($request, $response) {

			$this->request = new Request($request);
			$this->response = new Response($response);

			$this->ast = $this->ast();

		}

		protected function ast() {
			return Routes::ast();
		}

		protected function execute($route) {

			list($controller, $action, $actionName) = $this->prepareHandler($route);

			$this->printHandler($controller, $actionName);
			$this->invokeHandler($controller, $action);

		}

		protected function findHandler($verb, $path) {
			return $this->ast->$verb->crawl($this->ast->$verb->pathComponents($path), true);
		}

		protected function invokeHandler($controller, $action) {

			try {
				$controller = $controller::invoke($action, [$this->request, $this->response]);
				// $controller->execute($action, $this->request, $this->response);
			}
			catch (Exception $e) {

				Log::error($e->getMessage());
				Log::error($e->getTraceAsString());

			}

			$controller = null;

		}

		protected function populateParameters($route, $params) {

			if (!empty($route->parameters)) {

				foreach ($route->parameters as $i => $param) {

					if (!$this->validateParameter($route, $params, $i, $param)) {
						return false;
					}

					$this->request->addParameter($param, $params[$i]);

				}

			}

			return true;

		}

		protected function prepareControllerName($controller) {
			return Str::camelCase($controller)."Controller";
		}

		protected function prepareHandler($route) {

			$controller = "\\Agility\\Http\\Controller";
			if (Configuration::apiOnly()) {
				$controller = "\\Agility\\Http\\ApiController";
			}

			if (!empty($route->controller)) {
				$controller = $route->namespace.$this->prepareControllerName($route->controller);
			}

			$actionName = $route->action;
			if (is_a($route->action, Closure::class)) {
				$actionName = "Closure";
			}

			return [$controller, $route->action, $actionName];

		}

		protected function printHandler($controller, $action) {
			Log::info("Invoking ".$controller."::".$action."()");
		}

		protected function printRequest() {
			Log::info("Started ".strtoupper($this->request->method)." \"".$this->request->uri."\" for ".$this->request->ip." at ".date("Y-m-d H:i:s"));
		}

		protected function process404($route) {

			list($route404, $params) = $this->findHandler("get", "404");
			if ($route404 === false) {

				if (!empty($file404 = Configuration::document404())) {

					Log::info("Redirecting to 404.html");
					$this->response->redirect("/".$file404);

				}
				else {

					Log::info("Responding with HTTP/1.1 404");
					$this->response->status(404);
					$this->response->respond("");

				}

			}
			else {

				$this->request->addParameter("route", $route);
				$this->execute($route404);

			}

		}

		function serve() {

			$this->printRequest();

			gc_disable();

			list($route, $params) = $this->findHandler($this->request->method, $this->request->uri);
			if ($route === false) {
				return $this->process404($route);
			}

			if (!$this->validateRequest($route, $params)) {
				return $this->process404($route);
			}

			$this->execute($route);

			gc_collect_cycles();

		}

		protected function validateRequest($route, $params) {

			if (!$this->populateParameters($route, $params)) {
				return false;
			}

			return true;

		}

		protected function validateParameter($route, $params, $i, $param) {

			if (!$route->constraints->exists($param) || preg_match($route->constraints[$param], $params[$i]) == 1) {
				return true;
			}

			return false;

		}

	}

?>