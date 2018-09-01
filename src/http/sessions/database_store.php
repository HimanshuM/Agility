<?php

namespace Agility\Http\Sessions;

use Agility\Chrono\Chronometer;
use Agility\Config;

	class DatabaseStore {

		protected $model;

		function __construct($model) {
			$this->model = $model;
		}

		function readSession($sessionId) {

			$className = $this->model;
			$sessionObject = $className::find($sessionId);
			if ($sessionObject == false) {
				return [false, false];
			}

			$serializedSession = $sessionObject->data;
			$session = unserialize($serializedSession);

			if (Session::invalid($sessionObject->createdAt->timestamp)) {

				$sessionObject->delete();
				return [false, false];

			}

			return [$session, $sessionObject->createdAt->timestamp];

		}

		function writeSession($session) {

			$className = $this->model;
			if ($session->fresh) {

				$className::create(function($s) use ($session) {

					$s->sessionId = $session->id;
					$s->data = serialize($session);
					$s->createdAt = Chronometer::fromTimestamp($session->ctime);

				});

			}
			else {

				$className = $this->model;
				$className::execute("UPDATE ".$className::tableName()." SET data = ? WHERE ".$className::$primaryKey." = ?;", serialize($session), $session->id);

			}

		}

	}

?>