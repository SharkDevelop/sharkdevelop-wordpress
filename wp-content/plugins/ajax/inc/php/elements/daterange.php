<?php

class Ezfc_Element_Daterange extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		$datepicker_day_prices_default = Ezfc_Functions::get_object_value($this->data, "datepicker_day_prices_default", "");
		if (is_array($datepicker_day_prices_default)) {
			$datepicker_day_prices_default = array_map("trim", $datepicker_day_prices_default);
			$datepicker_day_prices_default = $this->get_value_special_fields($datepicker_day_prices_default);
		}

		$datepicker_day_prices = explode("\n", Ezfc_Functions::get_object_value($this->data, "datepicker_day_prices", ""));
		if (is_array($datepicker_day_prices)) {
			$datepicker_day_prices = array_map("trim", $datepicker_day_prices);
			$datepicker_day_prices = $this->get_value_special_fields($datepicker_day_prices);
		}

		$this->add_vars["data_settings"] = array(
			"available_days" => esc_attr(Ezfc_Functions::get_object_value($this->data, "available_days", "")),
			"blocked_days"   => esc_attr(Ezfc_Functions::get_object_value($this->data, "blocked_days", "")),
			"changeMonth"    => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_change_month", "")),
			"changeYear"     => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_change_year", "")),
			"datepicker_day_prices_default" => $datepicker_day_prices_default,
			"datepicker_day_prices" => $datepicker_day_prices,
			"daterange_count_full_days" => Ezfc_Functions::get_object_value($this->data, "daterange_count_full_days", 0),
			"firstDay"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "firstDay", "")),
			"numberOfMonths" => esc_attr(Ezfc_Functions::get_object_value($this->data, "numberOfMonths", 1)),
			"showAnim"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "showAnim", "")),
			"showWeek"       => esc_attr(Ezfc_Functions::get_object_value($this->data, "showWeek", "")),
			"yearRange"      => esc_attr(Ezfc_Functions::get_object_value($this->data, "datepicker_year_range", ""))
		);

		$this->element_js_vars = array_merge($this->element_js_vars, $this->add_vars["data_settings"]);

		// apply filter here so blocked_days can be automated with a booking system or something else
		$this->add_vars["data_settings"] = apply_filters("ezfc_daterange_settings", $this->add_vars["data_settings"], $this->data);
		$this->add_vars["data_settings_json"] = json_encode($this->add_vars["data_settings"]);

		if (empty($this->data->value)) $this->data->value = "";
		$add_attr = "";

		$preselected_dates = $this->data->value;
		if (!is_array($this->data->value)) {
			$preselected_dates = explode(";;", $preselected_dates);
		}
		
		$date_values = array(
			isset($preselected_dates[0]) ? $preselected_dates[0] : "",
			isset($preselected_dates[1]) ? $preselected_dates[1] : ""
		);

		$placeholder = explode(";;", $this->data->placeholder);
		$placeholder_values = array(
			isset($placeholder[0]) ? $placeholder[0] : "",
			isset($placeholder[1]) ? $placeholder[1] : ""
		);

		// mindate
		$minDate = explode(";;", $this->data->minDate);
		$minDate_values = array(
			isset($minDate[0]) ? $minDate[0] : "",
			isset($minDate[1]) ? $minDate[1] : ""
		);
		// maxdate
		$maxDate = explode(";;", $this->data->maxDate);
		$maxDate_values = array(
			isset($maxDate[0]) ? $maxDate[0] : "",
			isset($maxDate[1]) ? $maxDate[1] : ""
		);

		// special values
		$date_values = $this->get_value_special_fields($date_values);

		// workdays only
		if (!empty($this->data->workdays_only)) $add_attr .= " data-workdays_only='{$this->data->workdays_only}'";

		$add_attr_minDate = "data-initvalue='" . esc_attr($date_values[0]) . "'";
		$add_attr_maxDate = "data-initvalue='" . esc_attr($date_values[1]) . "'";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) {
			$add_attr_minDate .= " autocomplete='false'";
			$add_attr_maxDate .= " autocomplete='false'";
		}
		// readonly
		if (property_exists($this->data, "read_only") && $this->data->read_only == 1) {
			$add_attr_minDate .= " readonly";
			$add_attr_maxDate .= " readonly";
		}

		// if daterange_single is enabled, only the "to"-datepicker will be shown
		$class_container  = "";
		$class_from       = "";
		$class_to         = "";
		$daterange_single = Ezfc_Functions::get_object_value($this->data, "daterange_single", 0);

		if ($daterange_single != 0) {
			$class_container .= " ezfc-element-daterange-single ezfc-element-daterange-hide-from";

			// hide to
			if ($daterange_single == 1) $class_from = "ezfc-element-daterange-hidden";
			// hide from
			else if ($daterange_single == 2) $class_to = "ezfc-element-daterange-hidden";
		}

		// element needs an additional container class
		$el_text .= "<div class='ezfc-element-daterange-container {$class_container}' data-factor='{$this->data->factor}' data-settings='{$this->add_vars["data_settings_json"]}' {$add_attr}>";

		// from
		$datepicker_output_from = "<input id='{$this->output["element_child_id"]}-from' class='{$this->data->class} ezfc-element ezfc-element-input ezfc-element-daterange ezfc-element-daterange-from {$class_from}' type='text' {$this->output["factor"]} name='{$this->output["element_name"]}[0]' value='{$date_values[0]}' {$this->add_vars["data_value_external"]} data-mindays='{$this->data->minDays}' data-maxdays='{$this->data->maxDays}' placeholder='{$placeholder_values[0]}' data-mindate='{$minDate_values[0]}' data-maxdate='{$maxDate_values[0]}' {$this->output["style"]} {$this->output["required"]} {$add_attr_minDate} />";

		// to
		$datepicker_output_to = "<input id='{$this->output["element_child_id"]}-to' class='{$this->data->class} ezfc-element ezfc-element-input ezfc-element-daterange ezfc-element-daterange-to {$class_to}' type='text' {$this->output["factor"]} name='{$this->output["element_name"]}[1]' value='{$date_values[1]}' {$this->add_vars["data_value_external"]} data-mindays='{$this->data->minDays}' data-maxdays='{$this->data->maxDays}' placeholder='{$placeholder_values[1]}' data-mindate='{$minDate_values[1]}' data-maxdate='{$maxDate_values[1]}' {$this->output["style"]} {$this->output["required"]} {$add_attr_maxDate} />";

		// build output
		$datepicker_output = $datepicker_output_from . $datepicker_output_to;

		/*// always visible -> wrap in divs
		$always_visible = true;
		if ($always_visible) {
			$datepicker_output = "<div class='ezfc-element-daterange-always-visible'>{$datepicker_output_from}</div><div class='ezfc-element-daterange-always-visible'>{$datepicker_output_to}</div>";
		}*/

		$el_text .= $datepicker_output;

		// end container
		$el_text .= "</div>";

		return $el_text;
	}

	/**
		returns the formatted calculated value
	**/
	public function get_summary_value_formatted() {
		// invalid data
		if (!is_array($this->submission_value)) return "";

		$date_from = trim($this->submission_value[0]);
		$date_to   = trim($this->submission_value[1]);

		// no value
		if (empty($date_from) || empty($date_to)) return "";

		return "{$date_from} - {$date_to}";
	}

	/**
		returns the formatted calculated value
	**/
	public function get_summary_value_calculated() {
		// enable currency temporarily
		$this->data->is_currency = 1;
		
		return $this->format_number_force($this->calculated_submission_value);
	}
}