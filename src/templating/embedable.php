<?php

namespace Agility\Templating;

use Agility\Configuration;
use ArrayUtils\Arrays;

	trait Embedable {

		protected $cssCache;
		protected $jsCache;
		protected $title;

		function css() {

			$args = func_get_args();
			if (count($args) == 0) {
				return $this->echoCss();
			}

			foreach ($args as $arg) {
				$this->cssCache[] = $arg;
			}

			if (empty($this->layout)) {
				return $this->echoCss();
			}

		}

		protected function cssLink($css) {
			return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->getEmbedablePath($css)."\">";
		}

		protected function echoCss() {

			foreach ($this->cssCache as $css) {
				echo $this->cssLink($css);
			}

		}

		protected function echoJs() {

			foreach ($this->jsCache as $js) {
				echo $this->jsLink($js);
			}

		}

		protected function getEmbedablePath($resource, $css = true) {

			if ($css) {
				return Configuration::cssPath().$resource.".css";
			}
			else {
				return Configuration::jsPath().$resource.".js";
			}

		}

		function js() {

			$args = func_get_args();
			if (count($args) == 0) {
				return $this->echoJs();
			}

			foreach ($args as $arg) {
				$this->jsCache[] = $arg;
			}

			if (empty($this->layout)) {
				return $this->echoJs();
			}

		}

		protected function jsLink($js) {
			return "<script src=\"".$this->getEmbedablePath($js, false)."\"></script>";
		}

		function title($title = null) {

			if (is_string($title)) {
				$this->title = $title;
			}

			return $this->title;

		}

	}

?>