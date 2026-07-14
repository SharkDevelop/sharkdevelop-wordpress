<?php

class Ezfc_Element_Stepend extends Ezfc_Element_Decor {
	public function get_output() {
		$this->step = true;
		$el_text    = "";

		$el_text .= "<div class='ezfc-clear'></div>";

		if (property_exists($this->data, "add_line") && $this->data->add_line == 1) {
			$el_text .= "<hr class='ezfc-step-line' />";
		}

		// step button wrapper
		$el_text .= "<div class='ezfc-step-button-wrapper'>";

		// previous button
		if ($this->add_vars["current_step"] > 0) {
			$el_text .= "	<button class='ezfc-step-button ezfc-step-previous'>{$this->data->previous_step}</button>";
		}
		// next button
		if ($this->add_vars["current_step"] < $this->add_vars["step_count"] - 1) {
			$el_text .= "	<button class='ezfc-step-button ezfc-step-next'>{$this->data->next_step}</button>";

			// loading icon
			$el_text .= "	<div class='ezfc-step-loading-wrapper'>";
			$el_text .= "		<span class='ezfc-step-submit-icon'><i class='" . esc_attr(get_option("ezfc_loading_icon", "fa fa-cog fa-spin")) . "'></i></span>";
			$el_text .= "	</div>";
		}

		// end button wrapper
		$el_text .= "</div>";
		
		// end step wrapper
		$el_text .= "</div>";

		return $el_text;
	}
}