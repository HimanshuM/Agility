<?php

namespace Agility\Http;

// use Agility\Configuration AS Config;
use Agility\Config;

	class Configuration {

		static function initialize() {

			Config::sessionStore(new Sessions\Configuration);
			Config::forceSsl(false);

		}

	}

?>