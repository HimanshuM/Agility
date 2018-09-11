<?php

namespace Agility\Templating;

	trait HtmlTags {

		protected $noClosing = [
			"meta",
			"link"
		];

		function img($src, $width = -1, $height = -1, $options = []) {

			$attributes = $options["attributes"] ?? [];

			if (is_string($src)) {
				$attributes["src"] = $src;
			}
			else if (isset($src["base64"])) {

				$src = "data:".($src["data"] ?? "image/jpg").";";
				if (!empty($src["charset"])) {
					$src .= "charset=".$src["charset"].";";
				}

				$attributes["src"] = "base64 ".$src["base64"];

			}

			if ($width > -1) {
				$attributes["width"] = $width;
			}
			if ($height > -1) {
				$attributes["height"] = $height;
			}

			return $this->tagBuilder("img", $attributes);

		}

		function input($type = "text", $options = []) {

			$attributes = $options["attributes"] ?? [];
			$attributes["type"] = $type;

			return $this->tagBuilder("input", $attributes, $options["class"] ?? [], $options["data"] ?? [], true);

		}

		function tag($name, $options = [], $open = false) {

			if ($name == "input") {
				return $this->input($options["type"] ?? "text", $options);
			}
			else if ($name == "img") {
				return $this->img($options);
			}

			return $this->tagBuilder($name, $options["attributes"] ?? [], $options["class"] ?? [], $options["data"] ?? [], $open, $options["content"] ?? "");

		}

		protected function tagBuilder($name, $attributes = [], $class = [], $data = [], $open = false, $content = "") {

			$tag = "<".$name;

			if (!empty($attributes)) {

				$attr = [];
				foreach ($attributes as $key => $value) {
					$attr[] = "$key=\"$value\"";
				}

				$tag .= " ".implode(" ", $attr);

			}

			if (!empty($class)) {
				$tag .= " class=\"".implode(" ", $class);
			}

			if (!empty($data)) {

				$dataAttr = [];
				foreach ($data as $key => $value) {
					$dataAttr[] = "data-$key=\"$value\"";
				}

				$tag .= " ".implode(" ", $dataAttr);

			}

			if ($open) {
				$tag .= "/>";
			}
			else if (in_array($name, $this->noClosing)) {
				$tag .= ">";
			}
			else {

				$tagContent = "";
				if (!empty($content)) {

					if (is_string($content)) {
						$tagContent = $content;
					}
					else if (is_callable($content)) {
						$tagContent = $content();
					}

					$tagContent = "\n".$tagContent."\n";

				}

				$tag .= ">$tagContent</".$name.">";

			}

			echo $tag."\n";

		}

	}

?>