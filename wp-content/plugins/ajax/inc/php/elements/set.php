<?php

class Ezfc_Element_Set extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		$this->add_vars["data_settings"] = json_encode(array(
			"calculate_when_hidden" => isset($this->data->calculate_when_hidden) ? $this->data->calculate_when_hidden : 1,
			"precision"             => isset($this->data->precision)             ? $this->data->precision : 2,
			"price_format"          => isset($this->data->price_format)          ? $this->data->price_format : "",
		));

		$data_set_output = array();
		foreach ($this->data->set as $set_element) {
			if (!empty($set_element->target)) {
				$data_set_output[] = $set_element->target;
			}
		}

		$this->element_js_vars = array_merge($this->element_js_vars, array(
			"set_allow_zero"   => $this->data->set_allow_zero,
			"set_dom_selector" => esc_attr($this->data->set_dom_selector),
			"set_elements"     => $data_set_output,
			"set_operator"     => $this->data->set_operator,
			"set_use_factor"   => $this->data->set_use_factor
		));

		// text_only
		$el_text = apply_filters("ezfc_element_output_text_only", $el_text, $this->data, $this->options);
		
		$el_text .= "<input class='{$this->data->class} ezfc-element ezfc-element-input ezfc-element-set' type='text' name='{$this->output["element_name"]}' value='' {$this->output["style"]} />";

		return $el_text;
	}
}