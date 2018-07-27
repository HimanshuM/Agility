<?php

namespace Agility\Console\Commands;

use FileSystem\File;

	class HelpCommand extends Base {

		function perform($args) {

			if ($args->empty) {
				$file = new File(__DIR__."/generators/help/README");
			}
			else {
				$file = new File(__DIR__."/generators/help/".$args->first);
			}

			echo $file->read();

		}

	}

?>