<?php

namespace Agility\Server;

use ArrayUtils\Arrays;
use AttributeHelper\Accessor;

	final class Request {

		use Accessor;

		protected $request;

		protected $ip;
		protected $method;
		protected $params;
		protected $uri;

		protected $get;
		protected $post;

		function __construct($request) {

			$this->request = $request;
			$this->params = new Arrays;
			$this->compileParameters();

			$this->readonly("ip", "method", "params", "uri", "get", "post");

		}

		function __debugInfo() {
			return ["ip" => $this->ip, "method" => $this->method, "params" => $this->params, "uri" => $this->uri];
		}

		function addParameter($name, $value) {
			$this->params[$name] = $value;
		}

		protected function compileParameters() {

			$this->ip = $this->request->server["remote_addr"];
			$this->method = strtolower($this->request->server["request_method"]);
			$this->uri = $this->request->server["path_info"];

			$this->get = new Arrays($this->request->get);
			$this->post = new Arrays($this->request->post);

			if ($this->method != "get") {

				if (empty($this->request->post)) {
					$this->post = new Arrays(json_decode($this->request->rawContent, true));
				}

			}

		}

	}

?>