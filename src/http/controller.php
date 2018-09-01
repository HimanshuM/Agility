<?php

namespace Agility\Http;

use Agility\Data\Model;
use Agility\Routing\Routes;
use Agility\Templating\Render;
use Closure;

	class Controller extends ApiController {

		use Render;

		protected $request;
		protected $response;

		private $_invoked = false;

		function __construct() {

			parent::__construct();
			$this->initializeTemplating();

		}

		protected function conclude($response) {

			if (!$this->_responded) {

				if (empty($this->_content)) {

					$this->render($response);
					$this->respond(["html" => $this->_content]);

				}
				else {
					$this->respond(["html" => $this->_content]);
				}

			}

		}

		function redirectTo($location, $status = 302) {

			if (is_string($location)) {
				$this->redirectToLocation($location, $status);
			}
			else if (is_a($location, Model::class)) {

				$location = Routes::findRouteForModel($location);
				$this->redirectToLocation($location, $status);

			}
			else {
				throw new Exceptions\InvalidHttpLocationException($location, true);
			}

		}

		function redirectToLocation($location, $status = 302) {

			if (!is_string($location)) {
				throw new Exceptions\InvalidHttpLocationException($location);
			}

			$location = "/".trim($location, "/ ");

			$this->_responded = true;

			if (!is_int($status)) {
				throw new Exceptions\InvalidHttpStatusException($status);
			}

			$this->response->redirect($location, $status);

		}

	}

?>