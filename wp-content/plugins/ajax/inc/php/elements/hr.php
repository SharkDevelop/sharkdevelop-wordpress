<?php

class Ezfc_Element_Hr extends Ezfc_Element_Decor {
	public function get_output() {
		$el_text = "<hr class='{$this->data->class}' {$this->output["style"]} />";

		return $el_text;
	}
}