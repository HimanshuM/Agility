<?php

namespace Agility\Http\Sessions;

use Agility\Chrono\Chronometer;
use Agility\Config;
use Agility\Http\Cookie;
use ArrayUtils\Arrays;

	class Session extends Arrays {

		protected $id;
		protected $cookie;
		// Unset when the session was created from cookie or header
		protected $fresh = true;
		protected $createdAt;

		function __construct() {

			parent::__construct();

			if (!Config::sessionStore()->cookieStore) {
				$this->initializeId();
			}

			$this->createdAt = new Chronometer;

			$this->cookie = new Cookie(Config::sessionStore()->cookieName);

			$this->readonly("id", "cookie", "createdAt", "fresh");

		}

		static function buildFromCookie($cookie) {

			if (Config::sessionStore()->cookieStore) {

				if (($session = Config::sessionStore()->cookieStore->readSession($cookie)) === false) {
					return new Session;
				}

				$session->fresh = false;
				return $session;

			}
			else {
				return Session::buildFromBackend($cookie);
			}

		}

		static function buildFromHeader($header) {

			$sessionId = false;
			if (Config::sessionStore()->sessionSource["header"] == "authorization") {

				$authorization = Config::sessionStore()->sessionSource["authorization"] ?? "bearer";
				$authorization = ucfirst($authorization);
				$sessionId = str_replace($authorization, "", $header);

			}
			else {
				$sessionId = $header;
			}

			return Session::buildFromBackend(trim($sessionId));

		}

		static function buildFromBackend($sessionId) {

			$session = false;
			if (Config::sessionStore()->fileStore) {
				list($session, $createdAt) = Config::sessionStore()->fileStore->readSession($sessionId);
			}
			else {
				list($session, $createdAt) = Config::sessionStore()->databaseStore->readSession($sessionId);
			}

			if ($session !== false) {

				$session->id = $sessionId;
				$session->createdAt = $createdAt;
				$session->fresh = false;

			}
			else {
				$session = new Session;
			}

			return $session;

		}

		protected function initializeId() {
			$this->id = hash("sha256", microtime());
		}

		static function invalid($createdAt) {
			return mktime() - $createdAt->timestamp > Config::sessionStore()->expiry;
		}

		function persist($response) {

			if (Config::sessionStore()->fileStore) {
				$this->persistToFile();
			}
			else if (Config::sessionStore()->databaseStore) {
				$this->persistToDb();
			}

			$this->write($response);

		}

		protected function persistToCookie() {

		}

		protected function persistToDb() {
			Config::sessionStore()->databaseStore->writeSession($this);
		}

		protected function persistToFile() {
			Config::sessionStore()->fileStore->writeSession($this);
		}

		function serialized() {
			return serialize($this);
		}

		protected function write($response) {

			if (Config::sessionStore()->cookieStore) {
				Config::sessionStore()->cookieStore->writeSession($this, $response);
			}
			else {

				if (!$this->fresh || Config::sessionStore()->sessionSource != "cookie") {
					return;
				}

				$this->cookie->value = $this->id;
				$this->cookie->write($response);

			}

		}

	}

?>