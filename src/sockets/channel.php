<?php

namespace Agility\Sockets;

use Agility\Logger\Log;
use Agility\Server\Parameter;
use Throwable;

	abstract class Channel {

		protected $channel;
		protected $request;
		protected $params;
		protected $session;

		function __construct() {
			$this->params = new Parameter;
		}

		static function invoke($channel, $server, $request) {

			$object = new $channel($server, $request);
			$object->channel = $server;
			$object->request = $request;
			$object->session = $request->identifySession();
			$object->params->merge($object->request->getParams);

			try {
				$object->connect();
			}
			catch (Throwable $e) {

				unset($object);
				Log::error("Disconnecting websocket connection because: ".$e->getMessage());
				$server->disconnect($request->request->fd);
				return false;

			}

			$object->subscribed();
			$object->enqueue();

			return $object;

		}

		protected function enqueue() {

		}

		function connect() {

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