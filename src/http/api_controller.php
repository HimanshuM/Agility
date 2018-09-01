<?php

namespace Agility\Http;

use Agility\Server\AbstractController;

	class ApiController extends AbstractController {

		protected $request;
		protected $response;

		protected $session;

		private $_invoked = false;
		protected $_status = 200;

		function __construct() {
			parent::__construct();
		}

		protected function conclude($response) {
			$this->json($response);
		}

		function json($data, $status = 200) {

			$data = is_string($data) ? $data : json_encode($data);
			$this->respond(["json" => $data], $status);

		}

		protected function prepareParams($args) {

			$this->request = $args[0];
			$this->response = $args[1];
			$this->session = $this->request->identifySession();

			$this->params->merge($this->request->params);
			$this->params->merge($this->request->get);
			$this->params->merge($this->request->post);

		}

		function respond($response, $status = 200) {

			if ($this->_responded) {
				return;
			}

			$this->_responded = true;

			if (!is_int($status)) {
				throw new Exceptions\InvalidHttpStatusException($status);
			}
			$this->response->status($status);

			if (!$this->session->empty) {
				$this->session->persist($this->response);
			}

			if (isset($response["html"])) {

				$this->response->header("Content-Type", "text/html");
				$this->response->write($response["html"]);

			}
			else if (isset($response["json"])) {

				$this->response->header("Content-Type", "application/json");
				$this->response->write($response["json"]);

			}

			$this->response->end();

		}

	}

?>