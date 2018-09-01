<?php

namespace Agility\Templating;

use ArrayUtils\Arrays;

	trait Render {

		use Embedable;

		protected $layout = "layout/base";
		protected $templateBase;
		protected $template;
		protected $subContent = "";

		function content() {
			return $this->subContent;
		}

		protected function initializeTemplating() {

			$this->templateBase = "app/views/";
			$this->template = new Template($this->templateBase, $this);

			$this->cssCache = new Arrays;
			$this->jsCache = new Arrays;

		}

		// Accepts an optional template name and an array
		// Possible keys of the array: "json", "view", "status", "data"
		function render() {

			$template = null;
			$partial = false;
			$templateTried = "NA";
			$options = [];
			$data = null;
			$status = 200;

			$args = func_get_args();
			foreach ($args as $arg) {

				if (is_string($arg) && empty($template)) {
					$template = $arg;
				}
				else if (is_array($arg) || is_a($arg, Arrays::class)) {
					$options = $arg;
				}
				else if (is_object($arg)) {
					$data = $arg;
				}

			}

			if (isset($options["json"])) {
				return $this->json($options["json"], true);
			}

			if (is_null($template)) {
				$template = $this->getRelativeClassName()."/".$this->_methodInvoked;
			}

			if (!empty($template) || !empty($options["view"])) {

				$templateTried = $options["view"] ?? $template;
				$template = $this->template->templateExists($templateTried);
				$data = $data ?? $options["data"] ?? [];

			}
			else if (!empty($options["partial"])) {

				$templateTried = $options["partial"];
				$partial = true;
				$template = $this->template->templateExists($templateTried) ?: $this->template->templateExists($this->getRelativeClassName()."/".$templateTried) ?: false;
				$data = $data ?? $options["local"] ?? [];

			}

			if (empty($template)) {

				if (!empty($options["no_error"])) {
					return "";
				}

				throw new ViewNotFoundException($templateTried, $partial);

			}

			$data = $this->template->load($template, $data);
			if (!$partial) {

				$this->_content = $this->renderBase($data);
				$this->_status = $options["status"] ?? $this->_status;

			}

			return $data;

		}

		function renderBase($data) {

			if (empty($this->layout)) {
				return $data;
			}

			$this->subContent = $data;

			return $this->template->load($this->layout);

		}

	}

?>