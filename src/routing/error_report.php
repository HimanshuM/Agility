<?php

namespace Agility\Routing;

use Agility\Configuration;
use Agility\Http\Exceptions\HttpException;
use Agility\Logger\Log;
use Agility\Server\Response;
use Agility\Templating\Template;
use Closure;
use FileSystem\FileSystem;

	trait ErrorReport {

		protected static $rescuers = [];

		protected function exceptionGetSource($e) {

			$sourceFile = $e->getFile();
			$lines = FileSystem::open($sourceFile)->lines;

			$offset = $e->getLine();
			if ($offset < 9) {
				$offset = 0;
			}
			else {
				$offset -= 9;
			}

			$slicedLines = array_slice($lines, $offset, $e->getLine() - $offset + 10, true);

			return [$sourceFile, $slicedLines, $e->getLine() - 1];

		}

		protected function invokeRescuer($exception, $response) {

			$with = static::$rescuers[get_class($exception)];
			if (is_array($with)) {

				$obj = $with[0];
				$method = $with[1];

				if (is_a($method, Closure::class)) {
					return ($method->bindTo($obj, $obj))($exception, $response);
				}
				else {
					return $obj->$method($exception, $response);
				}

			}

			return $with($exception, $response);

		}

		protected function reportError($exception, $die = false) {

			if (!empty(static::$rescuers[get_class($exception)])) {
				return $this->invokeRescuer($exception, $this->response);
			}

			$this->response->status(500);
			if (Configuration::environment() != "development") {

				Log::error($exception, ["request" => $this->request]);
				$this->response->respond("");

			}
			else {

				$template = new Template(FileSystem::path(__DIR__."/views"), $this);
				$this->response->respond($template->load("500.php", ["e" => $exception, "r" => $this->request]));

			}

			if ($die) {
				die;
			}

		}

		static function rescueFrom($exception, $with) {
			static::$rescuers[$exception] = $with;
		}

	}

?>