<?php

class Ezfc_Element_Matrix extends Ezfc_Element {
	public function get_output() {
		$el_text  = "";
					
		$this->element_js_vars["conditional"] = array(
			"action"             => array(),
			"chain"              => array(),
			"notoggle"           => array(),
			"operator"           => array(),
			"option_index_value" => array(),
			"redirects"          => array(),
			"row_operator"       => array(),
			"target"             => array(),
			"target_value"       => array(),
			"use_factor"         => array(),
			"values"             => array()
		);

		$matrix_action = Ezfc_Functions::get_object_value($this->data, "matrix_action", "set");

		$i = 0;
		foreach ($this->data->matrix->target_values as $row => $row_array) {
			foreach ($row_array as $column => $target_value) {
				// invalid row
				if (!isset($this->data->matrix->conditions[$row]) || 
					!property_exists($this->data->matrix->conditions[$row], "elements")) continue;

				$this->element_js_vars["conditional"]["action"][$i]   = $matrix_action;
				$this->element_js_vars["conditional"]["operator"][$i] = "always";

				$this->element_js_vars["conditional"]["chain"][$i] = array(
					"compare_target" => array(),
					"operator"       => array(),
					"value"          => array(),
				);
				// conditional chain
				foreach ($this->data->matrix->conditions[$row]->elements as $ci => $cond_element) {
					$this->element_js_vars["conditional"]["chain"][$i]["compare_target"][$ci] = $cond_element;
					$this->element_js_vars["conditional"]["chain"][$i]["operator"][$ci]       = $this->data->matrix->conditions[$row]->operators[$ci];
					$this->element_js_vars["conditional"]["chain"][$i]["value"][$ci]          = $this->data->matrix->conditions[$row]->values[$ci];
				}

				$this->element_js_vars["conditional"]["target"][$i] = $this->data->matrix->target_elements[$column];
				$this->element_js_vars["conditional"]["target_value"][$i] = $target_value;

				$this->element_js_vars["conditional"]["notoggle"][$i]           = array("0");
				$this->element_js_vars["conditional"]["option_index_value"][$i] = array("");
				$this->element_js_vars["conditional"]["redirects"][$i]          = array("");
				$this->element_js_vars["conditional"]["row_operator"][$i]       = array(0);
				$this->element_js_vars["conditional"]["use_factor"][$i]         = array(0);
				$this->element_js_vars["conditional"]["values"][$i]             = array("");

				$i++;
			}
		}

		return $el_text;
	}
}