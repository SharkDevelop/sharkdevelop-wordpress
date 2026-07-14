<?php

class Ezfc_Element_Payment extends Ezfc_Element_Options {
	public function get_output() {
		$el_text  = "";

		// option container
		$el_text .= "<div class='ezfc-element-option-container'>";

		$element_options = $this->frontend->get_options_source($this->data, $this->id, $this->options);

		$i = 0;
		foreach ($element_options as $n => $option) {
			$add_data         = "";
			$selected         = false;
			$el_icon          = "";
			$el_image         = "";
			$el_wrapper_class = "";
			$selectable_text  = Ezfc_Functions::get_object_value($this->data, "options_text_only", 0) == 1;

			$radio_id   = "{$this->output["element_id"]}-{$i}";
			$radio_text = apply_filters("ezfc_option_label", $option->text, $radio_id);

			if (!empty($option->image) || !empty($option->icon) || $selectable_text) {
				// option image
				if (!empty($option->image)) {
					$el_image = $option->image;
				}
				// option icon
				if (!empty($option->icon)) {
					$el_icon = $option->icon;
					$el_wrapper_class .= " ezfc-element-option-has-icon";
				}
				// selectable text
				if ($selectable_text) {
					$el_wrapper_class .= " ezfc-element-option-has-selectable-text";
				}

				$el_wrapper_class .= " ezfc-element-option-has-image";
			}

			$el_text .= "<div class='ezfc-element-radio-container ezfc-element-single-option-container {$el_wrapper_class}'>";

			if (property_exists($this->data, "preselect") && $this->data->preselect == $i && $this->data->preselect != -1) {
				$selected = true;
				$add_data .= " data-initvalue='{$i}'";
			}

			if (property_exists($option, "disabled")) {
				$add_data .= " disabled='disabled'";
			}

			if (property_exists($this->data, "value")) {
				// preselect from WC cart (compare with $n) or from GET-parameter
				if (($this->add_vars["use_cart_values"] && $this->data->value == $n) || ($this->data->value == $option->value && $this->data->value != "" && $option->value != "")) {
					$selected = true;
				}
			}

			if ($selected) {
				$add_data .= " checked='checked'";
			}

			// option ID
			if (!empty($option->id)) {
				$add_data .= " data-optionid='" . esc_attr($option->id) . "'";
			}

			$show_price = $this->frontend->get_show_price_text($this->options, $this->data, $option->value, $this->type);

			$el_text .= "<div class='ezfc-element-radio'>";
			$el_text .= "	<input class='{$this->data->class} ezfc-element-radio-input' id='{$radio_id}' type='radio' name='{$this->output["element_name"]}' value='{$i}' data-value='{$option->value}' data-index='{$i}' data-factor='{$option->value}' data-settings='{$this->add_vars["data_settings"]}' {$this->output["style"]} {$add_data}>";

			// selectable items
			if (!empty($el_image) || !empty($el_icon) || $selectable_text) {
				$el_image_style = "";
				$img_class = "";

				// max width
				if (!empty($this->data->max_width)) {
					if (is_numeric($this->data->max_width)) $this->data->max_width .= "px";

					$el_image_style .= "max-width: {$this->data->max_width};";
				}
				// max height
				if (!empty($this->data->max_height)) {
					if (is_numeric($this->data->max_height)) $this->data->max_height .= "px";

					$el_image_style .= "max-height: {$this->data->max_height};";
				}

				// preselect
				if (!empty($selected)) {
					$img_class .= " ezfc-selected";
				}

				// selectable text
				if ($selectable_text) {
					$radio_text = "";
					$el_text .= "<div class='ezfc-element-option-image ezfc-element-option-selectable-text ezfc-element-radio-image {$img_class}' rel='{$this->output["element_id"]}' style='{$el_image_style}'>{$option->text}</div>";
				}
				// selectable image
				else if (!empty($el_image)) {
					$el_text .= "<img class='ezfc-element-option-image ezfc-element-radio-image {$img_class}' src='{$el_image}' rel='{$this->output["element_id"]}' alt='' style='{$el_image_style}' />";
				}
				// selectable icon
				else if (!empty($el_icon)) {
					$el_text .= "<div class='ezfc-element-option-image ezfc-element-radio-image ezfc-element-icon-wrapper {$img_class}' rel='{$this->output["element_id"]}' style='{$el_image_style}'><i class='ezfc-element-radio-icon fa fa-fw {$option->icon}'></i></div>";

					if (empty($this->data->option_add_text_icon)) {
						$radio_text = "";
					}
				}
			}
			// addon label
			$el_text .= "	<label class='ezfc-addon-label' for='{$radio_id}'></label>";
			$el_text .= "		<div class='ezfc-addon-option'></div>";
			$el_text .= "</div>";

			// text + price
			$el_text .= "<div class='ezfc-element-radio-text'>{$radio_text}<span class='ezfc-element-radio-price'>{$show_price}</span></div>";
			$el_text .= "<div class='ezfc-element-radio-clear'></div>";

			$el_text .= "</div>";

			$i++;
		}

		// option container
		$el_text .= "</div>";

		return $el_text;
	}
}