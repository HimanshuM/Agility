<?php

namespace Agility;

use Agility\Data\Connection\Pool;
use ArrayUtils\Arrays;
use Exception;
use StringHelpers\Str;

	class Application {

		protected $_swoole = null;

		function __construct() {

			// if (!defined("APP_PATH") || !Configuration::initialized()) {
			// 	$this->initialize();
			// }
			if (Configuration::environment() == "production") {
				$this->initialize();
			}

		}

		protected function configure() {

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

		protected function executePreInitializers() {
			Initializers\PreInitializer::execute();
		}

		protected function initialize() {

			// $environment = getenv("AGILITY_ENV") ?: "development";
			// $host = getenv("AGILITY_HOST") ?: "localhost";
			// $port = getenv("AGILITY_PORT") ?: "8000";

			// Configuration::initialize($environment, $host, $port);

			Configuration::uploadDir("storage");

		}

		function initializeComponents() {

			$this->initializeLogging();
			$this->initializeDatabase();
			$this->initializeRouting();
			$this->setupApplicationAutoloader();

		}

		protected function initializeDatabase() {
			Pool::initialize();
		}

		protected function initializeLogging() {

		}

		protected function initializeRouting() {

		}

		protected function initializeSwoole() {
			$this->_swoole = new \swoole_http_server(Configuration::host(), Configuration::port());
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

			$this->printBootupSequence();

			$this->executePreInitializers();
			$this->initializeComponents();
			$this->prepareApplication();

			$this->initializeSwoole();
			if (empty($this->_swoole)) {

				echo "Failed to initialize Swoole HTTP server. Something went wrong...";
				return;

			}

			$this->configureSwoole();
			$this->setupListner();

			$this->configure();

			echo "Listening on http://".Configuration::host().":".Configuration::port()."\n";
			$this->_swoole->start();

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

			echo "Started ".$request->server["request_method"]." \"".$request->server["path_info"]."\" for ".$request->server["remote_addr"]." at ".date("Y-m-d H:i:s")."\n";

			gc_disable();

			$controller = new \App\Controllers\ApplicationController;
			try {
				$controller->execute("index", $request, $response);
			}
			catch (Exception $e) {
				error_log($e->getMessage());
			}

			$controller = null;
			gc_collect_cycles();

		}

	}

?>