<?php

class Ezfc_Element_Starrating extends Ezfc_Element {
	public function get_output() {
		$el_text  = "";
					
		//$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";

		// container
		$el_text .= "<div class='ezfc-element-option-container'>";

		// wrapper
		$el_text .= "<div class='ezfc-rating'>";

		// number of stars
		$stars  = abs(max(1, (int) $this->data->stars));
		$stars *= !empty($this->data->half_stars) ? 2 : 1;

		for ($i = $stars; $i > 0; $i--) {
			$add_data   = "";
			$star_id    = "{$this->output["element_child_id"]}_{$i}";
			$star_value = !empty($this->data->half_stars) ? $i / 2 : $i;
			$star_class = is_float($star_value) ? "ezfc-rating-half" : "ezfc-rating-full";

			if (!empty($this->data->value) && $this->data->value == $i) {
				$add_data .= " checked='checked'";
			}

			$el_text .= "<input type='radio' id='{$star_id}' name='{$this->output["element_name"]}' value='{$star_value}' {$add_data} /><label class='{$star_class}' for='{$star_id}'></label>";
		}

		// wrapper end
		$el_text .= "</div>";
		// option container
		$el_text .= "</div>";

		return $el_text;
	}

	public function get_summary_value_formatted() {
		$user_value = (int) $this->submission_value;

		return "{$user_value}/{$this->data->stars}";
	}
}