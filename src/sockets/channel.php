<?php

namespace Agility\Sockets;

use Agility\Server\Parameter;
use Throwable;

	abstract class Channel {

		protected $channel;
		protected $request;
		protected $params;

		function __construct() {
			$this->params = new Parameter;
		}

		static function invoke($channel, $server, $request) {

			$object = new $channel($server, $request);
			$object->channel = $server;
			$object->request = $request;
			$object->params->merge($object->request->getParams);

			$object->subscribed();
			$object->enqueue();

			return $object;

		}

		protected function enqueue() {

		}

		abstract function subscribed();
		abstract function onMessage($frame);
		abstract function onClose();

		protected function respond($message) {

			try {
				$this->channel->push($this->request->request->fd, $message);
			}
			catch (Throwable $e) {
				$this->onClose();
			}

		}

	}