<?php

defined( 'ABSPATH' ) OR exit;

abstract class EZFC_Zapier {
	static function init() {
		if (!function_exists("curl_version")) return;

		add_action("ezfc_after_submission", array(__CLASS__, "after_submission"), 10, 7);
	}

	static function after_submission($insert_id, $total, $user_mail, $form_id, $output_data, $submission_data, $replace_values) {
		$form_options = $submission_data["options"];

		// check if zapier is enabled
		if (!get_option("ezfc_zapier_enabled", 1) || !$form_options["zapier_enabled"]) return;

		// verify hook URL
		if (empty($form_options["zapier_webhook_url"]) || !filter_var($form_options["zapier_webhook_url"], FILTER_VALIDATE_URL)) return;

		// filter
		$zapier_webhook_url = apply_filters("ezfc_zapier_webook_url", $form_options["zapier_webhook_url"], $insert_id, $form_id);
		$zapier_data        = apply_filters("ezfc_zapier_data", $submission_data["submission_elements_values_raw"], $insert_id, $form_id);

		// add custom data
		$zapier_data["__form_id"] = $submission_data["form_id"];
		$zapier_data["__submission_id"] = $submission_data["submission_id"];
		$zapier_data["__invoice_id"] = $submission_data["invoice_id"];

		self::post_to_zapier($zapier_webhook_url, $zapier_data);
	}

	static function post_to_zapier($webhook_url, $data) {
	    $curl = curl_init();

	    $curl_options = array(
	        CURLOPT_URL             => $webhook_url,
	        CURLOPT_RETURNTRANSFER  => true,
	        CURLOPT_CUSTOMREQUEST   => "POST",
	        CURLOPT_POST            => 1,
	        CURLOPT_POSTFIELDS      => $data
	    );

	    curl_setopt_array($curl, $curl_options);

	    $result = curl_exec($curl);

	    curl_close($curl);
	}
}

EZFC_Zapier::init();