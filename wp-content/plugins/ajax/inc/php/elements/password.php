<?php

class Ezfc_Element_Password extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";

		// readonly
		if (!empty($this->data->read_only)) $add_attr .= " readonly";
		// max length
		if (property_exists($this->data, "max_length") && $this->data->max_length != "") $add_attr .= " maxlength='{$this->data->max_length}'";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) $add_attr .= " autocomplete='false'";

		$el_text .= "<input	class='{$this->data->class} ezfc-element ezfc-element-input' {$this->output["factor"]} id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' placeholder='{$this->data->placeholder}' type='password' value='{$this->data->value}' {$add_attr} {$this->output["style"]} {$this->output["required"]} />";

		// double-check
		if (property_exists($this->data, "double_check") && $this->data->double_check == 1) {
			$double_check_name = "ezfc_element[{$this->id}_double_check]";
			$el_text .= "<br><input class='{$this->data->class} ezfc-element ezfc-element-input' name='{$double_check_name}' type='password' value='{$this->data->value}' placeholder='{$this->data->placeholder}' {$this->output["style"]} {$this->output["required"]} /> <small>" . __("Confirmation", "ezfc") . "</small>";
		}

		return $el_text;
	}
}