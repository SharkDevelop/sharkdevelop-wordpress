<?php

class Ezfc_Element_Table_order extends Ezfc_Element {
	public function get_output() {
		global $element;
		global $element_data;
		global $element_vars;
		global $output;

		$element = $this;
		$element_data = $this->data;

		$el_text  = "";
		$add_attr = "";

		$this->element_js_vars["table_order"] = array();

		// add table columns counter to element data
		$element_data->__table_columns = 2;

		$this->element_js_vars["table_order_loop_function"] = Ezfc_Functions::get_object_value($element_data, "table_order_loop_function", "");

		$element_options = $this->frontend->get_options_source($this->data, $this->id, $this->options);
		$output = $this->output;

		$el_text .= "<div class='{$this->data->class} ezfc-element ezfc-element-table_order' id='{$this->output["element_child_id"]}' {$this->output["style"]} {$this->output["required"]} {$add_attr}>";

		$el_text .= "<table class='ezfc-element-table_order-table'>";

		// check for images first
		$has_image = false;
		foreach ($element_options as $n => $option) {
			if (!empty($option->image)) {
				$has_image = true;
				break;
			}
		}

		// update table columns
		if ($has_image) $element_data->__table_columns++;
		if (Ezfc_Functions::get_object_value($element_data, "show_item_price", 1) == 1) $element_data->__table_columns++;
		if (Ezfc_Functions::get_object_value($element_data, "show_subtotal_column", 1) == 1) $element_data->__table_columns++;

		// add table header
		if (Ezfc_Functions::get_object_value($element_data, "table_order_add_header", 0) == 1) {
			$el_text .= $this->frontend->get_template("elements/table_order-header");
		}

		$el_text .= "<tbody>";
		foreach ($element_options as $n => $option) {
			$input_id = "{$this->output["element_child_id"]}-{$n}";
			$price_column_id = $input_id . "-price";
			$row_subtotal_id = $input_id . "-subtotal";

			$min = property_exists($option, "min") ? $option->min : 0;
			$max = property_exists($option, "max") ? $option->max : 0;
			$input_value = max(0, $min);

			$add_data = "";

			// option ID
			if (!empty($option->id)) {
				$add_data .= " data-optionid='" . esc_attr($option->id) . "'";
			}
			// disabled
			if (!empty($option->disabled)) {
				$add_data .= " disabled='disabled'";
			}

			$item_price = $this->frontend->number_format($option->value, $this->data);

			$element_vars = array(
				"add_data"        => $add_data,
				"disabled"        => !empty($option->disabled),
				"do_shortcode"    => Ezfc_Functions::get_object_value($this->data, "do_shortcode", 0),
				"has_image"       => $has_image,
				"id"              => $option->id,
				"index"           => $n,
				"input_id"        => $input_id,
				"input_value"     => $input_value,
				"item_price"      => $item_price,
				"min"             => $min,
				"max"             => $max,
				"option"          => $option,
				"price_column_id" => $price_column_id,
				"subtotal_id"     => $row_subtotal_id,
				"text_prefix"     => Ezfc_Functions::get_object_value($option, "text_prefix", ""),
				"text_suffix"     => Ezfc_Functions::get_object_value($option, "text_suffix", ""),
				"value"           => $option->value
			);
			// js
			$this->element_js_vars["table_order"][$n] = $element_vars;

			$el_text .= $this->frontend->get_template("elements/table_order-loop");
		}
		$el_text .= "</tbody>";

		// add table footer
		if (Ezfc_Functions::get_object_value($element_data, "table_order_add_footer", 0) == 1) {
			$el_text .= $this->frontend->get_template("elements/table_order-footer");
		}

		$el_text .= "</table>";
		$el_text .= "</div>";

		return $el_text;
	}

	/**
		returns the formatted selected option values
	**/
	public function get_summary_value_options($return_type = "text") {
		// invalid data
		if (!is_array($this->submission_value)) return "";

		$allow_html_in_options = get_option("ezfc_allow_option_html", 0);

		$return_value = array();
		$return_value[] = "<table><thead></thead><tbody>";

		// check for options source
		$element_values = (array) $this->frontend->get_options_source($this->data, $this->id, $this->options);

		foreach ($element_values as $i => $option) {
			// invalid data or conditionally hidden
			if (!isset($this->submission_value[$i]) || strpos($this->submission_value[$i], "__HIDDEN__") !== false) continue;

			if (($this->submission_value[$i] == 0 && $this->data->show_empty_values_in_email == 1) || ($this->submission_value[$i] != 0 && $this->data->show_empty_values_in_email == 0) || $this->data->show_empty_values_in_email == 1) {
				$loop_item_val = (int) $this->submission_value[$i];
				$loop_item_val_calculated = $loop_item_val * $option->value;
				$loop_item_val_formatted = $this->format_number($loop_item_val_calculated, true);

				$option_text = $option->text;
				// escape option text
				if ($allow_html_in_options == 0) {
					$option_text = esc_html($option_text);
				}
				else if ($allow_html_in_options == 1) {
					$option_text = htmlspecialchars_decode($option_text);
				}

				$return_value[] = "<tr>";
				$return_value[] = "<td style='padding: 5px; vertical-align: top;'>{$loop_item_val}x</td>";
				$return_value[] = "<td style='padding: 5px; vertical-align: top;'>{$option_text}</td>";
				$return_value[] = "<td style='padding: 5px; vertical-align: top; text-align: right; white-space: nowrap;'>{$loop_item_val_formatted}</td>";
				$return_value[] = "</tr>";
			}
		}

		$return_value[] = "</tbody><tfoot></tfoot></table>";

		return implode("", $return_value);
	}

	public function get_test_value() {
		$element_options = $this->frontend->get_options_source($this->data, $this->id, $this->options);
		
		$return_value = array();
		foreach ($element_options as $n => $option) {
			$max = $option->max == 0 ? 100 : $option->max;

			$return_value[$n] = Ezfc_Functions::get_random_number($option->min, $max);
		}

		return $return_value;
	}
}