<?php

class Ezfc_Element_Datepicker extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		$this->add_vars["data_settings"] = array(
			"available_days" => esc_attr(Ezfc_Functions::get_object_value($this->data, "available_days", "")),
			"blocked_days"   => esc_attr(Ezfc_Functions::get_object_value($this->data, "blocked_days", "")),
			"changeMonth"    => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_change_month", "")),
			"changeYear"     => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_change_year", "")),
			"firstDay"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "firstDay", "")),
			"minDate"        => esc_attr(Ezfc_Functions::get_object_value($this->data, "minDate", "")),
			"maxDate"        => esc_attr(Ezfc_Functions::get_object_value($this->data, "maxDate", "")),
			"numberOfMonths" => esc_attr(Ezfc_Functions::get_object_value($this->data, "numberOfMonths", 1)),
			"showAnim"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "showAnim", "")),
			"showWeek"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "showWeek", "")),
			"yearRange"      => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_year_range", ""))
		);

		// apply filter here so blocked_days can be automated with a booking system or something else
		$this->add_vars["data_settings"] = apply_filters("ezfc_datepicker_settings", $this->add_vars["data_settings"], $this->data);
		$this->add_vars["data_settings_json"] = json_encode($this->add_vars["data_settings"]);

		$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) $add_attr .= " autocomplete='false'";

		// readonly
		if (property_exists($this->data, "read_only") && $this->data->read_only == 1) {
			$add_attr .= " readonly";
		}

		$el_text .= "<input id='{$this->output["element_child_id"]}' class='{$this->data->class} ezfc-element ezfc-element-input ezfc-element-datepicker' type='text' name='{$this->output["element_name"]}' {$this->add_vars["data_value_external"]} value='{$this->data->value}' placeholder='{$this->data->placeholder}' {$this->output["style"]} {$this->output["required"]} {$add_attr} data-settings='{$this->add_vars["data_settings_json"]}' />";

		return $el_text;
	}

	public function get_test_value() {
		$date_format_jqueryui = empty($this->form_wrapper->options["datepicker_format"]) ? "dd/mm/yy" : $this->form_wrapper->options["datepicker_format"];

		// convert to PHP date
		$date_format = Ezfc_Functions::date_jqueryui_to_php($date_format_jqueryui);

		return date($date_format);
	}
}