<?php

class Ezfc_Element_Custom_calculation extends Ezfc_Element {
	public function get_output() {
		$el_text  = "";
		
		// default calculation code
		$custom_calculation = $this->data->custom_calculation;

		// generated function id
		$function_id_rand = substr(md5(microtime()), rand(0, 26), 10);
		$function_id = "ezfc_custom_calculation_" . $function_id_rand;

		// custom calculation function name
		$custom_calculation_function_name = Ezfc_Functions::get_object_value($this->data, "custom_calculation_function_name", "");
		// sanitize
		if (!empty($custom_calculation_function_name)) {
			$custom_calculation_function_name = sanitize_title($custom_calculation_function_name);
			$custom_calculation_function_name = str_replace("-", "_", $custom_calculation_function_name);

			// check if it's not empty after sanitizing function id
			if (!empty($custom_calculation_function_name)) {
				$function_id = $custom_calculation_function_name;
			}
		}

		// use existing function
		$custom_calculation_function_external = trim(Ezfc_Functions::get_object_value($this->data, "custom_calculation_function_external", ""));

		// safe function
		$safe_function_js_pre = "";
		$safe_function_js_suf = "";
		if (Ezfc_Functions::get_object_value($this->data, "custom_calculation_function_safe", 1) == 1) {
			$safe_function_js_pre = "if (typeof {$function_id} === 'undefined') {";
			$safe_function_js_suf = "}";
		}

		$el_text .= "<div class='ezfc-element-custom-calculation' data-function='{$function_id}'>";
		$el_text .= "<script>";
		$el_text .= $safe_function_js_pre;
		$el_text .= "function " . $function_id . "(price) {";

		// use existing function
		if (!empty($custom_calculation_function_external)) {
			$el_text .= "if (typeof {$custom_calculation_function_external} === 'function') { return {$custom_calculation_function_external}(price, {$this->id}); }";
		}
		// use custom function
		else {
			// init js
			if (Ezfc_Functions::get_object_value($this->data, "custom_calculation_init_add", 1) == 1) {
				$init_function_id = "EZFC_JS_INIT_{$function_id_rand}";
				$init_function_js = Ezfc_Functions::get_object_value($this->data, "custom_calculation_init_js", "");
				$init_function_js = $this->replace_magic_values($init_function_js);

				$el_text .= "if (typeof {$init_function_id} === 'undefined') { {$init_function_id} = true;";
				$el_text .= apply_filters("ezfc_custom_calculation_init_js", $init_function_js, $this->data, $this->form->id);
				$el_text .= "}";
			}

			// read from file
			if (Ezfc_Functions::get_object_value($this->data, "custom_calculation_function_read_file", 0) == 1) {

				$code_file = Ezfc_Functions::get_object_value($this->data, "custom_calculation_function_filename", "");
				if (!empty($code_file)) {
					// replace magic values
					$code_file = str_replace("{{abspath}}", trailingslashit(ABSPATH), $code_file);
					$code_file = str_replace("{{ezfc_path}}", trailingslashit(EZFC_PATH), $code_file);
					$code_file = str_replace("wp-config.php", "", $code_file); // just in case
					// upload path
					$upload_path = wp_upload_dir();
					$code_file = str_replace("{{upload_path}}", trailingslashit($upload_path["basedir"]), $code_file);

					// read file
					if (file_exists($code_file)) {
						$custom_calculation = file_get_contents($code_file);
					}
				}
			}

			// custom calculation js
			$custom_calculation = $this->replace_magic_values($custom_calculation);
			$el_text .= apply_filters("ezfc_custom_calculation_js", $custom_calculation, $this->data, $this->form->id);
		}

		$el_text .= "\n;return price;";
		$el_text .= "}";
		$el_text .= $safe_function_js_suf;
		$el_text .= "</script>";
		$el_text .= "</div>";

		$el_text .= "<input class='ezfc-element-custom-calculation-input' name='{$this->output["element_name"]}' type='hidden' value='' />";

		// text_only
		$el_text = apply_filters("ezfc_element_output_text_only", $el_text, $this->data, $this->options);

		// add wrapper so the special no-padding style doesn't apply
		if (!empty($this->data->text_only) && property_exists($this->data, "wrapper_class")) {
			$this->data->wrapper_class .= " ezfc-has-label";
		}

		return $el_text;
	}

	public function replace_magic_values($code) {
		// replace magic values
		$code = str_replace("__form_id__", $this->form->id, $code);
		// replace apos
		$code = str_replace("&#39;", "'", $code);
		// replace form elements
		$code = $this->frontend->replace_elementnames_with_ids($code, $this->add_vars["form_elements"]);

		return $code;
	}

	public function get_label() {
		if (empty($this->data->text_only)) {
			return "";
		}

		return $this->default_label;
	}

	/**
		returns the formatted submitted value
	**/
	public function get_summary_value_formatted() {
		return "";
	}
}