<?php

namespace Agility\Mailer;

use Agility\Application;
use Agility\Config;
use Agility\Chrono\Chronometer;
use Agility\Logger\Log;
use ArrayUtils\Arrays;
use PHPMailer\PHPMailer\PHPMailer;
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

		private function applicationInstance() {
			$this->applicationInstance = Application::instance();
		}

		function sendMail() {

			if (Config::mailer()->deliveryMethod == "none") {
				return Log::info("Delivery#sendMail skipped sending mail because delivery method is set to 'none'");
			}

			$phpMailerObj = new PhpMailer(true);
			$this->options->fill($phpMailerObj);

			if (Config::mailer()->deliveryMethod == "sendmail") {
				$phpMailerObj->isSendmail();
			}
			elseif (Config::mailer()->deliveryMethod != "mail") {
				$phpMailerObj->isSMTP();
			}

			return $phpMailerObj->send();

		}

		function sendAt($when = "now") {

			if ($when == "now") {
				$this->sendNow();
			}

		}

		function sendLater($after = 0) {
			Swoole\Timer::after($after, [$this, "sendMail"]);
		}

		function sendNow() {
			Swoole\Event::defer([$this, "sendMail"]);
		}

	}

?>