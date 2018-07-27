<?php

namespace Agility\Http;

use ActionTriggers\Trigger;
use Closure;

	class Controller {

		use Trigger;

		protected $request;
		protected $response;

		private $_invoked = false;

		function __construct() {

		}

		function execute($method, $request, $response) {

			$this->request = $request;
			$this->response = $response;

			$return = null;
			if (is_a($method, Closure::class)) {
				$return = ($method->bindTo($this))();
			}
			else {
				$return = $this->$method();
			}

			$this->render(["html" => $return]);

		}

		function html($template, $data) {

		}

		function json($data) {
			return $this->render(["json" => json_encode($data)]);
		}

		function render($options = []) {

			if ($this->_invoked) {
				return;
			}

			$this->_invoked = true;
			if (isset($options["html"])) {

				$this->response->header("Content-Type", "text/html");
				$this->response->write($options["html"]);

			}
			else if (isset($options["json"])) {

				$this->response->header("Content-Type", "application/json");
				$this->response->write($options["json"]);

			}
			$this->response->end();

		}

	}

?>