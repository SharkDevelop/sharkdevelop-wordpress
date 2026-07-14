<?php

class Ezfc_Element_Recaptcha extends Ezfc_Element_Decor {
	public function get_output() {
		$publickey = get_option("ezfc_captcha_public");
		
		$el_text = "<div class='g-recaptcha' data-sitekey='{$publickey}'></div>";

		$recaptcha_language = empty($this->data->language) ? "" : "?lang={$this->data->language}";

		wp_enqueue_script("ezfc-google-recaptcha", "//www.google.com/recaptcha/api.js{$recaptcha_language}");

		return $el_text;
	}
}