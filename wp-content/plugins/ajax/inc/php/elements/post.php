<?php

class Ezfc_Element_Post extends Ezfc_Element {
	public function get_output() {
		$el_text  = "";
					
		if ($this->data->post_id == 0) return $el_text;

		$post = get_post($this->data->post_id);

		$el_text .= $this->frontend->apply_content_filter($post->post_content);
		$el_text .= "<input class='ezfc-element-post' name='{$this->output["element_name"]}' type='hidden' value='1' />";

		return $el_text;
	}
}