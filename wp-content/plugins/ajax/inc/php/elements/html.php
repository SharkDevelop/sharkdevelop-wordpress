<?php

class Ezfc_Element_Html extends Ezfc_Element {
	public function get_output() {
		$el_text = "";
		$el_text .= "<div>";

		$html_content = wp_specialchars_decode(stripslashes($this->data->html));

		// replace magic values
		$html_content = str_replace("__form_id__", $this->form->id, $html_content);
		$html_content = str_replace(array("&quot;", "&#39;"), "'", $html_content);

		$this->element_js_vars["replace_placeholders_show_zero"] = Ezfc_Functions::get_object_value($this->data, "replace_placeholders_show_zero", 1);

		// shortcode
		if (!empty($this->data->do_shortcode)) {
			$html_content = do_shortcode($html_content);
		}
		// placeholders
		if (!empty($this->data->replace_placeholders)) {
			$html_content = $this->frontend->get_listen_placeholders($this->data, $html_content);
		}

		// linebreaks / content filter
		if (!empty($this->data->add_linebreaks)) {
			$html_content = wpautop($html_content);
			$html_content = $this->frontend->apply_content_filter($html_content);
		}

		// convert encoding
		if (isset($this->frontend->global_options["html_convert_encoding"]["from"]) && isset($this->frontend->global_options["html_convert_encoding"]["to"])) {
			$encoding_from = trim($this->frontend->global_options["html_convert_encoding"]["from"]);
			$encoding_to   = trim($this->frontend->global_options["html_convert_encoding"]["to"]);

			if (!empty($encoding_from) && !empty($encoding_to)) {
				$html_content = mb_convert_encoding($html_content, $encoding_to, $encoding_from);
			}
		}

		if ($this->frontend->global_options["html_force_convert_utf8"] == 1) {
			$html_content = utf8_encode($html_content);
		}

		$html_content = apply_filters("ezfc_html_element_content", $html_content);

		$el_text .= $html_content;

		$el_text .= "<input class='ezfc-element-html' name='{$this->output["element_name"]}' type='hidden' value='1' />";
		$el_text .= "</div>";

		return $el_text;
	}

	public function get_summary_value_formatted() {
		$value = $this->data->html;

		// shortcode
		if (!empty($this->data->do_shortcode)) {
			$value = do_shortcode($value);
		}

		// placeholders
		if (!empty($this->data->replace_placeholders)) {
			$value = $this->frontend->get_listen_placeholders($this->data, $value);
		}

		// linebreaks / content filter
		if (!empty($this->data->add_linebreaks)) {
			$value = wpautop($value);
			$value = $this->frontend->apply_content_filter($value);
		}

		return $value;
	}

	public function set_submission_value($value) {
		parent::set_submission_value($value);

		$value = $this->data->html;

		return $value;
	}

	public function submission_show_in_email() {
		// skip html elements since it is disabled in the form options
		if ($this->form_wrapper->options["email_show_html_elements"] == 0) return false;

		return parent::submission_show_in_email();
	}

	public function get_test_value() {
		return $this->submission_show_in_email() ? $this->get_summary_value_formatted() : "";
	}
}