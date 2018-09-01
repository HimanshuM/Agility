<?php

namespace Agility\Http\Sessions;

use Agility\Config;

	class FileStore {

		protected $storageLocation;

		function __construct() {

			Config::documentRoot()->mkdir("tmp/session");
			$this->storageLocation = Config::documentRoot()->chdir("tmp/session");

		}

		function readSession($sessionId) {

			if (($sessionFile = $this->sessionFile($sessionId)) === false) {
				return [false, false];
			}

			$content = $sessionFile->lines();

			$ctime = intval($content[0]);
			$serializedSession = $content[1];

			if (Session::invalid($ctime)) {

				$sessionFile->delete();
				return [false, false];

			}

			return [unserialize($serializedSession), $ctime];

		}

		function writeSession($session) {

			$sessionFile = $this->sessionFile($session->id, true);

			$serializedSession = serialize($session);
			$content = $session->ctime.PHP_EOL.$serializedSession;

			$sessionFile->write($content);

		}

		protected function sessionFile($sessionId, $create = false) {

			if (!$create) {

				// Any invalid session ID will not be used
				if ($this->storageLocation->has("sess_".$sessionId)) {
					return $this->storageLocation->touch("sess_".$sessionId);
				}
				else {
					return false;
				}

			}
			else {
				return $this->storageLocation->touch("sess_".$sessionId);
			}

		}

	}

?>