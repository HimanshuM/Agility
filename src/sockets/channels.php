<?php

namespace Agility\Sockets;

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
			$channel = Str::normalize($uri);

			$channel = Channels::getName($channel);
			Channels::$subscriptions[$request->request->fd] = Channel::invoke($channel, $server, $request);

		}

		protected static function getName($channel) {
			return "App\\Channels".$channel."Channel";
		}

		static function onMessage(Server $server, Frame $frame) {
			Channels::$subscriptions[$frame->fd]->onMessage($frame);
		}

		static function onClose(Server $server, int $fd) {

			try {
				Channels::$subscriptions[$fd]->onClose($fd);
			}
			catch (Throwable $e) {}

			unset(Channels::$subscriptions[$fd]);
			gc_collect_cycles();

		}

	}