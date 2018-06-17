<?php

namespace Agility\Generators;

	class NewGenerator extends Base {

		protected $_appName;

		protected function __construct($root, $args) {

			parent::__construct($root, $args, "new");
			$this->_appName = $this->_args->shift;
			$this->_parseOptions();

		}

		function appName() {
			echo $this->_appName;
		}

		protected function _generate() {

			parent::_generate();
			$this->echo("All done! Happy coding :)\n");

		}

		protected function _publish($template, $name, $data) {

			$name = $this->_sanitizeFileExtension($name);
			$this->echo("\t#B#create  #N#$name\n");

			$templateName = "";
			if ($template->isFile()) {

				$file = $this->_root->touch($name);
				$file->write($data);

				if ($name == "bin/agility") {
					$file->chmod(0755);
				}

			}
			else {
				$this->_root->mkdir($name);
			}

		}

		private function _sanitizeFileExtension($filename) {

			$extn = strpos($filename, ".at");
			if ($extn !== false && $extn == strlen($filename) - 3) {
				return substr($filename, 0, -3);
			}

			return $filename;

		}

	}

?>