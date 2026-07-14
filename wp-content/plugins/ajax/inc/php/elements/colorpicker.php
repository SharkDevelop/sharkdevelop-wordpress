<?php

class Ezfc_Element_Colorpicker extends Ezfc_Element {
	public function get_output() {
		$el_text  = "";
					
		wp_enqueue_style("ezfc-element-colorpicker-css", EZFC_URL . "assets/colorpicker/css/bootstrap-colorpicker.min.css");
		wp_enqueue_script("ezfc-element-colorpicker-js", EZFC_URL . "assets/colorpicker/js/bootstrap-colorpicker.min.js", array( 'jquery' ), false, false);

		$add_attr = "";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) $add_attr .= " autocomplete='false'";

		$el_text .= "<input class='{$this->data->class} ezfc-element-input ezfc-element-colorpicker-input' id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' type='text' value='{$this->data->value}' {$this->output["style"]} {$add_attr} />";
		$el_text .= "<div class='ezfc-element-colorpicker'><div></div></div>";

		return $el_text;
	}
}