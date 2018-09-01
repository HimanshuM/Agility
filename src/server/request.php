<?php

namespace Agility\Server;

use Agility\Configuration;
use Agility\Http\Sessions\Session;
use ArrayUtils\Arrays;
use AttributeHelper\Accessor;

	final class Request {

		use Accessor;

		protected $request;

		protected $headers;
		protected $ip;
		protected $method;
		protected $params;
		protected $uri;

		protected $get;
		protected $post;
		protected $cookie;

		function __construct($request) {

			$this->request = $request;
			$this->headers = new Arrays($request->header);
			$this->params = new Arrays;
			$this->compileParameters();

			$this->readonly("headers", "ip", "method", "params", "uri", "get", "post", "cookie");

		}

		function __debugInfo() {
			return ["ip" => $this->ip, "method" => $this->method, "params" => $this->params, "uri" => $this->uri, "cookie" => $this->cookie];
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
			if (!empty($this->request->cookie)) {
				$this->cookie = new Arrays($this->request->cookie);
			}
			else {
				$this->cookie = new Arrays;
			}

			if ($this->method != "get") {

				if (empty($this->request->post)) {
					$this->post = new Arrays(json_decode($this->request->rawContent, true));
				}

			}

		}

		function identifySession() {

			if (Configuration::sessionStore()->sessionSource == "cookie") {

				if ($this->cookie->exists(Configuration::sessionStore()->cookieName)) {
					return Session::buildFromCookie($this->cookie[Configuration::sessionStore()->cookieName]);
				}

			}
			else if (is_array(Configuration::sessionStore()->sessionSource) && isset(Configuration::sessionStore()->sessionSource["header"])) {

				$header = Configuration::sessionStore()->sessionSource["header"];
				if ($this->headers->exists($header)) {
					return Session::buildFromHeader($this->headers[$header]);
				}

			}

			return new Session;

		}

	}

?>