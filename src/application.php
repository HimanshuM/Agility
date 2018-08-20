<?php

namespace Agility;

use Agility\Data\Connection\Pool;
use Agility\Routing\Dispatch;
use Agility\Routing\Routes;
use Agility\Server\StaticContent;
use ArrayUtils\Arrays;
use AttributeHelper\Accessor;
use Exception;
use StringHelpers\Str;

	abstract class Application {

		use Accessor;

		protected $_swoole = null;
		protected static $_instance;

		function __construct() {

			// if (!defined("APP_PATH") || !Configuration::initialized()) {
			// 	$this->initialize();
			// }
			if (Configuration::environment() == "production") {
				$this->initialize();
			}

			$this->readonly(["swoole", "_swoole"]);
			static::$_instance = $this;

		}

		protected function configureSwoole() {

			$swooleConfig = [
				"document_root" => Configuration::documentRoot()->cwd."/public",
				"enable_static_handler" => true
			];

			if (!empty(Configuration::uploadDir())) {
				$swooleConfig["upload_tmp_dir"] = Configuration::uploadDir();
			}

			$this->_swoole->set($swooleConfig);

		}

		protected function determine404Response() {

			if (Configuration::apiOnly()) {
				Configuration::document404(false);
			}
			else {
				StaticContent::initialize();
			}

		}

		protected function executePreInitializers() {
			Initializers\PreInitializer::execute();
		}

		protected function executePostInitializers() {
			Initializers\PostInitializer::execute();
		}

		function firstStageInitialization() {

			try {
				$this->executePreInitializers();
			}
			catch (Exception $e) {
				die($e->getMessage()."\n");
			}

			$this->determine404Response();

			try {
				$this->initializeComponents();
			}
			catch (Exception $e) {
				die($e."\n");
			}

		}

		protected function initialize() {

			// $environment = getenv("AGILITY_ENV") ?: "development";
			// $host = getenv("AGILITY_HOST") ?: "localhost";
			// $port = getenv("AGILITY_PORT") ?: "8000";

			// Configuration::initialize($environment, $host, $port);

			Configuration::uploadDir("storage");

		}

		protected function initializeComponents() {

			$this->initializeLogging();
			$this->initializeDatabase();
			$this->initializeRouting();
			$this->setupApplicationAutoloader();

		}

		protected function initializeDatabase() {
			Pool::initialize();
		}

		protected function initializeLogging() {

			if (empty(Configuration::logPath())) {

				if (!Configuration::documentRoot()->has("log") && !Configuration::documentRoot()->mkdir("log")) {
					die("Failed to create log directory. Make sure the document root is writable.\n");
				}

				Configuration::logPath(Configuration::documentRoot()->chdir("log"));

			}

			Logger\Log::initialize();

		}

		protected function initializeRouting() {
			Routes::initialize();
		}

		protected function initializeSwoole() {
			$this->_swoole = new \swoole_http_server(Configuration::host(), Configuration::port());
		}

		static function instance() {
			return static::$_instance;
		}

		protected function prepareApplication() {
			AppLoader::loadModels();
		}

		protected function printBootupSequence() {

			echo "Agility application starting in ".Configuration::environment()." environment\n";
			echo "Use Ctrl+C to stop the server\n";
			echo "Booting Swoole HTTP server\n";

		}

		function run() {

			$this->firstStageInitialization();
			$this->printBootupSequence();

			try {
				$this->prepareApplication();
			}
			catch (Exception $e) {
				die($e."\n");
			}

			$this->initializeSwoole();
			if (empty($this->_swoole)) {

				echo "Failed to initialize Swoole HTTP server. Something went wrong...";
				return;

			}

			$this->secondStageInitialization();

			echo "Listening on http://".Configuration::host().":".Configuration::port()."\n";
			$this->_swoole->start();

		}

		function secondStageInitialization() {

			$this->configureSwoole();
			$this->setupListner();
			$this->executePostInitializers();

		}

		protected function setupApplicationAutoloader() {

			$cwd = Configuration::documentRoot()->cwd;
			spl_autoload_register(function($class) use ($cwd) {

				$components = new Arrays(explode("\\", $class));
				$class = $components->map(function($each) {
					return Str::snakeCase(lcfirst($each));
				})->implode("/");
				if (file_exists($class.".php")) {
					require_once($class.".php");
				}

			});

		}

		protected function setupListner() {
			$this->_swoole->on("request", [$this, "listner"]);
		}

		function listner($request, $response) {
			(new Dispatch($request, $response))->serve();
		}

	}

?>