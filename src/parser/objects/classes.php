<?php

namespace Agility\Parser\Objects;

	class Classes extends Type {

		protected $name = false;
		protected $staticMethods = [];
		protected $instanceMethods = [];
		protected $members = [];
		protected $isAbstract = false;
		protected $isFinal = false;
		public $type = "class";

		function __construct($name = false) {
			$this->name = $name;
		}

	}

?>