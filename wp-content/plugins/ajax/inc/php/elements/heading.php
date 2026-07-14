<?php

class Ezfc_Element_Heading extends Ezfc_Element_Decor {
	public function get_output() {
		$css_classes = $this->data->class;

		// apply label filters
		$title = $this->prepare_label($this->data->title);

		// alignment
		$element_alignment = Ezfc_Functions::get_object_value($this->data, "alignment", "default");
		if ($element_alignment != "default") {
			$css_classes .= " ezfc-text-{$element_alignment}";
		}

		// output
		$el_text = "<{$this->data->tag} class='{$css_classes}' {$this->output["style"]}>{$title}</{$this->data->tag}>";

		return $el_text;
	}

	public function get_label() {
		return "";
	}
}