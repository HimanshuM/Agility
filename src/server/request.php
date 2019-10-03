<?php

namespace Agility\Server;

use Agility\Configuration;
use Agility\Http\Sessions\Session;
use ArrayUtils\Arrays;
use AttributeHelper\Accessor;

	final class Request implements \JsonSerializable {

		use Accessor;

		protected $request;

		protected $host;
		protected $port;
		protected $headers;
		protected $ip;
		protected $method;
		protected $params;
		protected $uri;

		protected $getParams;
		protected $postParams;
		protected $cookie;

		protected $format;

		function __construct($request) {

			$this->request = $request;
			$this->headers = new Arrays($request->header);
			$this->params = new Arrays;
			$this->cookie = new Arrays;
			$this->compileParameters();

			$this->methodsAsProperties("delete", "get", "options", "patch", "post", "put");
			$this->readonly("host", "port", "headers", "ip", "method", "params", "uri", "getParams", "postParams", "cookie", "format");

		}

		function __debugInfo() {
			return ["ip" => $this->ip, "method" => $this->method, "params" => $this->params, "uri" => $this->uri, "cookie" => $this->cookie];
		}

		function addParameter($name, $value) {
			$this->params[$name] = $value;
		}

		function compileAcceptHeader($defaultAccept = "text/html") {
			$this->format = new RequestFormat($this->request->header["accept"] ?? "", $defaultAccept);
		}

		protected function compileParameters() {

			$this->host = parse_url($this->request->header["host"]);
			$this->ip = $this->request->server["remote_addr"];
			$this->method = strtolower($this->request->server["request_method"]);
			$this->uri = $this->request->server["path_info"];

			$this->getParams = new Arrays($this->request->get);
			$this->postParams = new Arrays($this->request->post);

			if (!empty($this->request->cookie)) {

				foreach ($this->request->cookie as $name => $value) {
					$this->cookie[$name] = $value;
				}

			}

			if ($this->method != "get") {

				if (empty($this->request->post)) {
					$this->post = new Arrays(json_decode($this->request->rawContent(), true));
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

		function delete() {
			return $this->method == "delete";
		}

		function get() {
			return $this->method == "get";
		}

		function options() {
			return $this->method == "options";
		}

		function patch() {
			return $this->method == "patch";
		}

		function post() {
			return $this->method == "post";
		}

		function put() {
			return $this->method == "put";
		}

		function jsonSerialize() {

			return [
				"host" => $this->host,
				"port" => $this->port,
				"ip" => $this->ip,
				"uri" => $this->uri,
				"params" => $this->params,
				"method" => $this->method,
				"get" => $this->getParams,
				"post" => $this->postParams,
				"headers" => $this->headers,
				"cookies" => $this->cookie,
				"files" => $this->fileParams->keys
			];

		}

		function toJson() {
			return json_encode($this->jsonSerialize());
		}

	}

?>