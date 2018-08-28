<?php

namespace Agility;

use Agility\Console\Helpers\EchoHelper;
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
		use EchoHelper;

		protected $_swoole = null;
		protected static $_instance;
		protected $quite;

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
				"enable_static_handler" => true,
				"buffer_output_size" => 32 * 1024 * 1024/* Set output buffer size to 32 MB */
			];

			if (Configuration::environment() == "production") {

				$swooleConfig["daemonize"] = 2;
				$swooleConfig["log_file"] = Configuration::documentRoot()."/log/swoole.log";
				$swooleConfig["log_level"] = 1;

			}

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
				$this->die($e->getMessage());
			}

			$this->determine404Response();

			try {
				$this->initializeComponents();
			}
			catch (Exception $e) {
				$this->die($e);
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

			$this->setupApplicationAutoloader();
			$this->setupComposerAutoloader();
			$this->initializeLogging();
			$this->initializeDatabase();
			$this->initializeRouting();
			$this->initializeMailer();

		}

		protected function initializeDatabase() {
			Pool::initialize();
		}

		protected function initializeLogging() {
			Logger\Log::initialize();
		}

		protected function initializeMailer() {
			Mailer\Base::initialize();
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

			$this->echo("Agility application starting in ".Configuration::environment()." environment");
			$this->echo("Use Ctrl+C to stop the server");
			$this->echo("Booting Swoole HTTP server");

		}

		function run() {

			$this->firstStageInitialization();
			$this->printBootupSequence();

			try {
				$this->prepareApplication();
			}
			catch (Exception $e) {
				$this->die($e);
			}

			$this->initializeSwoole();
			if (empty($this->_swoole)) {
				$this->die("Failed to initialize Swoole HTTP server. Something went wrong...");
			}

			$this->secondStageInitialization();

			$this->echo("Listening on http://".Configuration::host().":".Configuration::port());
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

		protected function setupComposerAutoloader() {

			if (($composerAutoLoader = Configuration::documentRoot()->has("vendor/autoload.php")) !== false) {
				require_once $composerAutoLoader;
			}

		}

		protected function setupListner() {
			$this->_swoole->on("request", [$this, "listner"]);
		}

		function listner($request, $response) {
			(new Dispatch($request, $response))->serve();
		}

	}

?>