<?php

namespace Agility\Server;

use Phpm\Exceptions\ClassExceptions\MethodNotFoundException;

	final class Response {

		protected $response;
		public $cookies = [];

		function __construct($response) {
			$this->response = $response;
		}

		function __call($name, $args = []) {

			if (method_exists($this->response, $name)) {

				if ($name == "write") {
					$this->sendCookies();
				}

				return call_user_func_array([$this->response, $name], $args);

			}

			throw new MethodNotFoundException("Agility\\Server\\Response", $name);

		}

		function redirect($location, $status = 302) {

			$this->response->status($status);
			$this->response->header("Location", $location);
			$this->response->end();

		}

		function respond($response, $status = 200) {

			if ($status != 200) {
				$this->response->status($status);
			}

			$this->sendCookies();

			$this->response->end($response);

		}

		function sendCookies() {

			var_dump($this->cookies);
			foreach ($this->cookies as $cookie) {
				$cookie->write($this);
			}

		}

	}

?>