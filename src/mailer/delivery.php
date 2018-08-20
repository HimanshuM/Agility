<?php

namespace Agility\Mailer;

use Agility\Application;
use Agility\Extensions\Chrono\Chronometer;
use ArrayUtils\Arrays;
use Swoole;

	class Delivery {

		protected $content;
		protected $options;
		protected $applicationInstance;

		function __construct($content, $options) {

			$this->content = $content;

			if (!is_a($options, Arrays::class)) {
				$options = new Arrays($options);
			}
			$this->options = $options;

		}

		function deliverAt($when = "now") {

		}

		function deliverLater($after = 0) {
			Swoole\Timer::after($after, [$this, "sendMail"]);
		}

		function deliverNow() {
			Swoole\Event::defer([$this, "sendMail"]);
		}

		private function applicationInstance() {
			$this->applicationInstance = Application::instance();
		}

		function sendMail() {

		}

	}

?>