<?php

class Ezfc_Element_Fileupload extends Ezfc_Element_Decor {
	public function get_output() {
		$el_text  = "";
					
		wp_enqueue_script("jquery-ui-progressbar");
		wp_enqueue_script("ezfc-jquery-file-upload", EZFC_URL . "assets/js/jquery.fileupload.min.js", array("jquery"));
		wp_enqueue_script("ezfc-jquery-iframe-transport", EZFC_URL . "assets/js/jquery.iframe-transport.min.js", array("jquery-ui-widget"));

		$this->element_js_vars["file_upload_auto"] = Ezfc_Functions::get_object_value($this->data, "file_upload_auto", 1);

		$multiple = $this->data->multiple==1 ? "multiple" : "";
		// file upload input
		$el_text .= "<input type='file' name='ezfc-files[]' class='{$this->data->class} ezfc-element-fileupload' id='{$this->output["element_child_id"]}' placeholder='{$this->data->placeholder}' {$multiple} {$this->output["style"]} />";

		if ($this->element_js_vars["file_upload_auto"] == 0) {
			// upload button
			$el_text .= "<button class='btn ezfc-upload-button' id='ezfc-upload-button-{$this->id}'>" . __("Upload", "ezfc") . "</button>";
		}

		// clearer
		$el_text .= "<div class='ezfc-clear'></div>";

		//$el_text .= "<div class='ezfc-fileupload-drop'>" . __("</div>";

		// progressbar
		$el_text .= '<div class="ezfc-progress ezfc-progress-striped active">
						<div class="ezfc-bar ezfc-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">
							<span class="sr-only">0% Complete</span>
						</div>
					</div>';

		// upload message
		$el_text .= "<p class='ezfc-fileupload-message'></p>";

		// uploaded files
		$el_text .= "<ul class='ezfc-fileupload-files'></ul>";

		return $el_text;
	}
}