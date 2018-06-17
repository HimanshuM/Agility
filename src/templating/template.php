<?php

namespace Agility\Templating;

use Closure;
use Exception;

use ArrayUtils\Arrays;
use FileSystem\FileSystem;

	class Template {

		/* Base path inside which a template would be searched */
		private $_basePath;

		/* Exposes entire objects to the template */
		private $_exposedObject;

		function __construct($basePath, $object) {

			if (is_string($basePath)) {
				$this->_basePath = FileSystem::path(trim($basePath, "/"));
			}
			else if (is_a($basePath, "FileSystem\\FileSystem")) {
				$this->_basePath = clone $basePath;
			}
			else {
				throw new Exception("Templates base path should be string or an object of class FileSystem\\FileSystem.", 1);
			}

			$this->_exposedObject = $object;

		}

		function __call($method, $args = []) {
			return call_user_func_array([$this->_exposedObject, $method], $args);
		}

		function __get($attr) {
			return $this->_exposedObject->$attr;
		}

		function __set($attr, $value) {
			$this->_exposedObject->$attr = $value;
		}

		function load($template) {

			ob_start();
			require_once($this->getTemplateName($template));
			$content = ob_get_clean();

			return $content;

		}

		private function getTemplateName($template) {

			if (($templatePath = $this->_basePath->has($template)) !== false) {
				return $templatePath;
			}
			else if (($templatePath = $this->_basePath->has($template.".php")) !== false) {
				return $templatePath;
			}
			else if (($templatePath = $this->_basePath->has($template.".php.at")) !== false) {
				return $templatePath;
			}
			else if (($templatePath = $this->_basePath->has($template.".at")) !== false) {
				return $templatePath;
			}

			return $template;

		}

	}

?>