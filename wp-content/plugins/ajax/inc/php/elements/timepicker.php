<?php

class Ezfc_Element_Timepicker extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		wp_enqueue_style("ezfc-jquery-timepicker", EZFC_URL . "assets/css/jquery.timepicker.css");
		wp_enqueue_script("ezfc-jquery-timepicker", EZFC_URL . "assets/js/jquery.timepicker.min.js", array("jquery"));

		$timepicker_settings = array(
			"disabledTimes" => isset($this->data->disabledTimes) ? $this->data->disabledTimes : "",
			"format"  => isset($this->data->format) ? $this->data->format : $this->options["timepicker_format"],
			"minTime" => isset($this->data->minTime) ? $this->data->minTime : "",
			"maxTime" => isset($this->data->maxTime) ? $this->data->maxTime : "",
			"steps"   => isset($this->data->steps) ? $this->data->steps : ""
		);

		$this->add_vars["data_settings"] = apply_filters("ezfc_timepicker_settings", $timepicker_settings, $this->element, $this->data);

		$this->add_vars["data_settings"] = json_encode($this->add_vars["data_settings"]);

		$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) $add_attr .= " autocomplete='false'";

		$el_text .= "<input class='{$this->data->class} ezfc-element ezfc-element-input ezfc-element-timepicker' type='text' id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' {$this->add_vars["data_value_external"]} value='{$this->data->value}' placeholder='{$this->data->placeholder}' {$this->output["style"]} {$this->output["required"]} data-settings='{$this->add_vars["data_settings"]}' {$add_attr} />";

		return $el_text;
	}
}