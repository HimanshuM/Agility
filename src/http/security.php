<?php

namespace Agility\Http;

use Agility\Configuration;
use AttributeHelper\Accessor;

	class Security {

		use Accessor;

		protected $encryptionKey = "";

		function __construct($encryptionKey) {

			$this->encryptionKey = $encryptionKey;
			$this->readonly("encryptionKey");

		}

		static function appHasSecurityFile($root) {
			return $root->has("config/security.json");
		}

		static function initialize() {

			if (($securityFile = Security::appHasSecurityFile(Configuration::documentRoot())) !== false) {
				Security::parseSecurityJson($securityFile);
			}

		}

		static function parseSecurityJson($securityJson) {

			$securityJson = json_decode(file_get_contents($securityJson), true);
			$securityJson = $securityJson[Configuration::environment()];

			Configuration::security(new Security(base64_decode($securityJson["encryption_key"])));

		}

	}

?>