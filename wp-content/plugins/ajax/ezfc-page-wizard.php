<?php

/**
	wizard page
**/

return; // temporarily disabled

defined( 'ABSPATH' ) OR exit;

$message = "";

if (!empty($_POST)) {
	require_once(EZFC_PATH . "class.ezfc_backend.php");
	$ezfc = Ezfc_backend::instance();

	// validate user
	$ezfc->validate_user("ezfc-nonce", "nonce");

	$settings_fields       = $ezfc->get_settings_fields(true);
	$settings_fields_assoc = Ezfc_Functions::array_index_key($settings_fields, "name");

	$form_options = array();
	$form_options_id_array = array();

	// exceptions
	// price format
	$price_format_tmp = "0,0[.]00";
	if ($_POST["ezfc_element"]["price_format"] == "default") {
		$_POST["ezfc_element"]["global__email_price_format_thousand"]  = ",";
		$_POST["ezfc_element"]["global__email_price_format_dec_point"] = ".";
	}
	else if ($_POST["ezfc_element"]["price_format"] == "eu") {
		$_POST["ezfc_element"]["global__email_price_format_thousand"]  = ".";
		$_POST["ezfc_element"]["global__email_price_format_dec_point"] = ",";
	}
	// always show decimal point
	if (isset($_POST["show_decimal_numbers"])) {
		$price_format_tmp = "0,0.00";
	}
	$_POST["ezfc_element"]["global__price_format"] = $price_format_tmp;

	foreach ($_POST["ezfc_element"] as $name => $value) {
		// update global option
		if (strpos($name, "global__") !== false) {
			$option_name = str_replace("global__", "", $name);

			update_option("ezfc_{$option_name}", $value);
		}
		// form option
		else if (strpos($name, "form__") !== false) {
			$option_name = str_replace("form__", "", $name);

			$form_options[$option_name] = $value;
		}
	}

	// change form options array to id index
	foreach ($form_options as $option_name => $option_value) {
		if (!isset($settings_fields_assoc[$option_name])) continue;

		$tmp_option = $settings_fields_assoc[$option_name];
		$form_options_id_array[$tmp_option["id"]] = $option_value;
	}

	$ezfc->update_options($form_options_id_array, true, false);

	// create paypal sites
	if (!empty($_POST["ezfc_element"]["pp_create_pages"])) {
		// return page
		$post_arr = array(
			'post_type'     => 'page',
			'post_title'    => __("PayPal Return Page", "ezfc"),
			'post_content'  => '[ezfc_verify /]',
			'post_status'   => 'publish'
		);
		$post_id_return = wp_insert_post( $post_arr );

		// cancel page
		$post_arr = array(
			'post_type'     => 'page',
			'post_title'    => __("PayPal Cancel Page", "ezfc"),
			'post_content'  => __("PayPal payment was cancelled.", "ezfc"),
			'post_status'   => 'publish'
		);
		$post_id_cancel = wp_insert_post( $post_arr );

		if (empty($post_id_return) || empty($post_id_cancel)) {
			$message .= __("Unable to create PayPal pages.", "ezfc");
		}
		else {
			// update ezfc paypal options
			$post_return_url = get_permalink($post_id_return);
			$post_cancel_url = get_permalink($post_id_cancel);

			update_option("ezfc_pp_return_url", $post_return_url);
			update_option("ezfc_pp_cancel_url", $post_cancel_url);
		}
	}

	$message = __("Options saved.", "ezfc");
}

// security nonce
$nonce = wp_create_nonce("ezfc-nonce");

?>

<style>
.ezfc .ezfc-theme-slick .ezfc-element-select {
	padding: 0.3em 1em;
}
</style>

<div class="ezfc wrap ezfc-wrapper container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<div class="inner">
				<h2><?php echo __("Wizard", "ezfc"); ?> - ez Form Calculator v<?php echo EZFC_VERSION; ?></h2> 
			</div>
		</div>

		<?php if (!empty($message)) { ?>
			<div class="col-lg-12">
				<div class="inner">
					<div id="message" class="updated"><?php echo $message; ?></div>
				</div>
			</div>
		<?php } ?>
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="inner">
				<?php
				Ezfc_shortcode::$add_script = true;
				Ezfc_shortcode::wp_head();
				echo do_shortcode("[ezfc preview='0' /]");
				Ezfc_shortcode::print_script();
				?>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$(".ezfc-element[name='ezfc_element[nonce]']").val("<?php echo $nonce; ?>");
});
</script>
