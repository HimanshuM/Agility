<?php

namespace Agility\Sockets;

use Agility\Logger\Log;
use Agility\Server\Request;
use Swoole\WebSocket\Server;
use Swoole\Http\Request as SwooleRequest;
use Swoole\WebSocket\Frame;
use StringHelpers\Str;
use Throwable;

	class Channels {

		protected static $subscriptions = [];

		static function listener(Server $server, SwooleRequest $request) {

			$request = new Request($request);
			if (strpos($request->uri, "/websockets") !== 0) {
				return $server->disconnect($request->request->fd);
			}

			$uri = substr($request->uri, strlen("/websockets"));
			Log::info("Received WebSocket request at $uri");
			$channel = Str::normalize($uri);

			$channel = Channels::getName($channel);
			$channel = Channel::invoke($channel, $server, $request);
			if (!empty($channel)) {
				Channels::$subscriptions[$request->request->fd] = $channel;
			}

		}

		protected static function getName($channel) {
			return "App\\Channels".$channel."Channel";
		}

		static function onMessage(Server $server, Frame $frame) {
			Channels::$subscriptions[$frame->fd]->onMessage($frame);
		}

		static function onClose(Server $server, int $fd) {

			try {

				if ($channel = Channels::$subscriptions[$fd] ?? false) {
					$channel->onClose($fd);
				}

			}
			catch (Throwable $e) {}

			unset(Channels::$subscriptions[$fd]);
			gc_collect_cycles();

		}

	}