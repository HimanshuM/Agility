<?php

namespace Agility\Http;

	class Controller {

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

		function render($options = []) {

			if ($this->_invoked) {
				return;
			}

			$this->_invoked = true;
			$this->response->write($options["html"]);
			$this->response->end();

		}

	}

?>