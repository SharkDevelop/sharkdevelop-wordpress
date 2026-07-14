<?php

class Ezfc_Element_Repeatable_form extends Ezfc_Element {
	public $elements = array();
	public $child_form;

	public function init() {
		if (!$this->is_valid()) return;

		$this->elements = Ezfc_Functions::array_index_key($this->frontend->form_elements_get($this->data->repeatable_form_id), "id");

		// add form
		$this->child_form = $this->frontend->add_form($this->data->repeatable_form_id);

		$element_counter = 0;
		$this->element_js_vars["__element_ids"] = array();
		foreach ($this->child_form->form_elements_objects as $element) {
			$element->set_parent_id($this->id);
			$element->set_form_options($this->form_wrapper->options);
			$element->init();

			$this->element_js_vars["__element_ids"][] = $element->id;
			$element_counter++;
		}

		$this->element_js_vars["repeatable"]                = $this->get_data_value("repeatable");
		$this->element_js_vars["repeatable_count_max"]      = $this->get_data_value("repeatable_count_max");
		$this->element_js_vars["repeatable_count_start"]    = $this->get_data_value("repeatable_count_start");
		$this->element_js_vars["__repeated_count"]          = 0;
		$this->element_js_vars["__repeated_elements_count"] = $element_counter;
		$this->element_js_vars["__repeated_ids"]            = $this->element_js_vars["__element_ids"];
	}

	public function before_form_output() {
		if (!$this->is_valid()) return;

		$this->child_form->options = $this->form_wrapper->options;

		// find ID of this element in form elements order array first
		$id_position = array_search($this->id, $this->form_wrapper->form_elements_order);

		// not found for some reason
		if ($id_position === false) return;

		// insert into position
		array_splice($this->form_wrapper->form_elements_order, $id_position, 0, array_keys($this->child_form->form_elements));
	}

	public function get_output() {
		if (!$this->is_valid()) return $this->get_error_message();

		$this->element_js_vars["repeatable_form_id"] = $this->data->repeatable_form_id;

		$form_output = $this->frontend->get_output($this->data->repeatable_form_id, null, null, null, null, 1, $this->form);

		if (!isset($this->form_wrapper->form_options_js["repeatable_forms"]) || !is_array($this->form_wrapper->form_options_js["repeatable_forms"])) {
			$this->form_wrapper->form_options_js["repeatable_forms"] = array();
		}
		$this->form_wrapper->form_options_js["repeatable_forms"][$this->data->repeatable_form_id] = 1;

		return $form_output;
	}

	public function get_wrapper_output($content, $output_data) {
		if (!$this->is_valid()) return $content;

		global $element_vars;

		$element_vars = array(
			"content"            => $content,
			"id"                 => $this->id,
			"repeatable"         => $this->get_data_value("repeatable"),
			"repeat_button_text" => $this->get_data_value("repeat_button_text"),
			"remove_button_text" => $this->get_data_value("remove_button_text"),
		);

		$output = $this->frontend->get_template("elements/repeatable_form");

		return $output;
	}

	/**
		returns the formatted submitted value
	**/
	public function get_summary_value_formatted() {
		if (!$this->is_valid()) return;

		$summary = "";

		$repeatable_form = $this->frontend->form_get($this->data->repeatable_form_id);

		if (!$repeatable_form || count($repeatable_form->form_elements) == 0 || !is_array($this->frontend->submission_data["raw_values"][$this->id])) return "";

		// get repeated count
		$summary_elements_prepared = array(
			"raw_values" => array(),
			"calculated_values" => array(),
		);

		// prepare submission values
		foreach ($this->frontend->submission_data["raw_values"][$this->id] as $repeated_element_id => $submitted_element_values) {
			if ($repeated_element_id == $this->id) continue;

			foreach ($submitted_element_values as $n => $element_value) {
				if (!isset($summary_elements_prepared["raw_values"][$n])) $summary_elements_prepared["raw_values"][$n] = array();
				if (!isset($summary_elements_prepared["calculated_values"][$n])) $summary_elements_prepared["calculated_values"][$n] = array();

				$summary_elements_prepared["raw_values"][$n][$repeated_element_id] = $element_value;

				// calculated value
				if (isset($this->frontend->submission_data["calculated_values"][$this->id][$repeated_element_id]) && isset($this->frontend->submission_data["calculated_values"][$this->id][$repeated_element_id][$n])) {
					$summary_elements_prepared["calculated_values"][$n][$repeated_element_id] = $this->frontend->submission_data["calculated_values"][$this->id][$repeated_element_id][$n];
				}
			}
		}

		$form = $this->frontend->form_get($this->data->repeatable_form_id);

		// get summary
		$summary_array = array();

		foreach ($summary_elements_prepared["raw_values"] as $i => $summary_row_raw) {
			$summary_row_calc = isset($summary_elements_prepared["calculated_values"][$i]) ? $summary_elements_prepared["calculated_values"][$i] : 0;

			$summary_array[] = $form->get_summary_form_elements(array(
				"content_only" => true,
				"form_elements" => $repeatable_form->form_elements_objects,
				"loop_value" => $this->form_wrapper->summary->loop_value,
				"values" => array(
					"raw_values" => $summary_row_raw,
					"calculated_values" => $summary_row_calc,
				)
			));
		}

		$summary = implode("", $summary_array);

		// update loop value from main form
		$this->form_wrapper->summary->loop_value += $form->summary->loop_value;

		return $summary;
	}

	public function get_test_value() {
		if (!$this->is_valid()) return;

		$return_value = array();

		foreach ($this->child_form->form_elements_objects as $n => $element) {
			$return_value[$n] = $element->get_test_value();
		}

		return $return_value;
	}

	public function is_valid() {
		return $this->get_error_message() === false;
	}

	public function get_error_message() {
		if ($this->get_data_value("repeatable_form_id") == 0) return __("No form selected.", "ezfc");
		else if ($this->form->id == $this->get_data_value("repeatable_form_id")) return __("Error: Repeatable form can't be the same as the parent form.", "ezfc");
		else if ($this->parent_id != 0) return sprintf(__("Error: Repeatable form (%s) contains another repeatable form (%s) which is currently not supported.", "ezfc"), $this->parent_id, $this->id);

		return false;
	}
}