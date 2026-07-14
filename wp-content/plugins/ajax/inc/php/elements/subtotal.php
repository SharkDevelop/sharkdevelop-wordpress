<?php

class Ezfc_Element_Subtotal extends Ezfc_Element {
	public function get_output() {
		global $element;
		global $element_data;
		global $element_vars;
		global $output;

		$element = $this;
		$element_data = $this->data;
		$output = $this->output;

		$el_text  = "";
		$add_attr = "";

		$this->add_vars["data_settings"] = json_encode(array(
			"calculate_when_hidden" => Ezfc_Functions::get_object_value($this->data, "calculate_when_hidden", 1),
			"precision"             => Ezfc_Functions::get_object_value($this->data, "precision", 2),
			"price_format"          => Ezfc_Functions::get_object_value($this->data, "price_format", ""),
		));

		// text_only
		$el_text = apply_filters("ezfc_element_output_text_only", $el_text, $this->data, $this->options);

		// readonly
		if (!empty($this->data->read_only)) $add_attr .= " readonly";

		// inline calculation
		if (!empty($this->data->inline_calculation)) {
			$function_id = "ezfc_inline_calculation_{$this->id}";
			$inline_calculation_text = $this->frontend->replace_elementnames_with_ids($this->data->inline_calculation, $this->form_wrapper->form_elements);

			$inline_calculation_func = "<script>if (typeof {$function_id} === 'undefined') function {$function_id}(price) { return {$inline_calculation_text}; }</script>";

			$this->element_js_vars["inline_calculation"] = $function_id;

			$el_text .= $inline_calculation_func;
		}

		// min/max
		if (property_exists($this->data, "min") && property_exists($this->data, "max")) {
			$add_attr .= " data-min='{$this->data->min}' data-max='{$this->data->max}'";
			$this->element_js_vars["min"] = $this->data->min;
			$this->element_js_vars["max"] = $this->data->max;
		}

		$this->element_js_vars["calculate_when_zero"] = Ezfc_Functions::get_object_value($this->data, "calculate_when_zero", 1);

		$this->add_vars["add_attr"] = $add_attr;
		$element_vars = $this->add_vars;

		$el_text .= $this->frontend->get_template("elements/subtotal");

		return $el_text;
	}

	public function get_summary_value_calculated() {
		return parent::get_summary_value_calculated();
	}

	public function get_summary_value_formatted() {
		return parent::get_summary_value_formatted();
	}
}