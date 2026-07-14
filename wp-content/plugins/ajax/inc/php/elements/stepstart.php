<?php

class Ezfc_Element_Stepstart extends Ezfc_Element_Decor {
	public function get_output() {
		$this->step = true;
		$el_text    = "";
					
		$step_class = "ezfc-step ezfc-step-start {$this->data->wrapper_class}";
		if ($this->add_vars["current_step"] == 0) $step_class .= " ezfc-step-active";

		$el_text = "<div class='{$step_class}' data-step='{$this->add_vars["current_step"]}' data-id='{$this->element->id}' id='ezfc-element_{$this->element->id}'>";

		if (!empty($this->data->title) && $this->options["step_use_titles"] == 0) {
			$step_display = absint($this->add_vars["current_step"]) + 1;
			$title = str_replace("{{n}}", $step_display, $this->data->title);
			$title = str_replace("%d", $step_display, $title);

			$el_text .= "<div class='ezfc-step-title {$this->data->class}'>{$title}</div>";
		}

		return $el_text;
	}

	public function prepare_label($custom_label = "") {
		$this->default_label = "";
	}
}