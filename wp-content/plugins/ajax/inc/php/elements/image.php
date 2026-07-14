<?php

class Ezfc_Element_Image extends Ezfc_Element_Decor {
	public function get_image() {
		$image_size = empty($this->data->featured_image_size) ? "large" : $this->data->featured_image_size;
		$image_url  = $this->data->image;

		// use featured image
		if (!empty($this->data->featured_image) && has_post_thumbnail($post->ID)) {
			$tmp_image  = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $image_size);
			if (isset($tmp_image[0])) $image_url = $tmp_image[0];
		}

		// check for post ID
		if (!empty($this->data->GET)) {
			if (isset($_GET[$this->data->GET]) && is_numeric($_GET[$this->data->GET])) {
				$tmp_image = wp_get_attachment_image_src(get_post_thumbnail_id($_GET[$this->data->GET]), $image_size);
				if (isset($tmp_image[0])) $image_url = $tmp_image[0];
			}
		}

		// use fallback image if no no or invalid image was set
		if (!filter_var($image_url, FILTER_VALIDATE_URL) && !empty($this->data->fallback_image) && filter_var($this->data->fallback_image, FILTER_VALIDATE_URL)) {
			$image_url = $this->data->fallback_image;
		}

		return $image_url;
	}

	public function get_output() {
		global $post;
		
		$el_text  = "";
					
		$image_url = $this->get_image();

		$el_text .= "<img src='{$image_url}' alt='" . esc_attr($this->data->alt) . "' class='{$this->data->class}' id='{$this->output["element_child_id"]}' {$this->output["style"]} />";

		return $el_text;
	}

	public function get_test_value() {
		return $this->get_output();
	}
}