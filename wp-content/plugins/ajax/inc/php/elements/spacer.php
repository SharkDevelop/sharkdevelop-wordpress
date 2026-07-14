<?php

class Ezfc_Element_Spacer extends Ezfc_Element_Decor {
	public function get_output() {
		$el_text  = "";
					
		$el_style = empty($this->data->style) ? "" : $this->data->style;

		// height
		if (!empty($this->data->height)) {
			if (is_numeric($this->data->height)) $this->data->height .= "px";

			$el_style .= "height: {$this->data->height};";
		}

		$el_text .= "<div class='ezfc-spacer' style='{$el_style}'></div>";

		return $el_text;
	}
}