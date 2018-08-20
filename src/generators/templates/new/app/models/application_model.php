<?php

namespace App\Models;

use Agility\Data\Model;

	abstract class ApplicationModel extends Model {

		protected static $abstract = true;

		static function initialize() {

		}

	}

?>