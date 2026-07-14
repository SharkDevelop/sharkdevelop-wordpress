<?php

/**
	licensing page
**/

defined( 'ABSPATH' ) OR exit;

require_once(EZFC_PATH . "class.ezfc_backend.php");
require_once(EZFC_PATH . "class.ezfc_functions.php");

$ezfc = Ezfc_backend::instance();

$message = "";
$message_class = "updated";

if (!empty($_POST["ezfc-request"]) && $ezfc->validate_user("ezfc-nonce", "nonce")) {
	// register license
	if ($_POST["action"] == "register_license" && !empty($_POST["purchase_code"])) {
		$result = Ezfc_Functions::register_license($_POST["purchase_code"]);

		if (!empty($result["error"])) {
			$message = $result["error"];
			$message_class = "notice notice-error";
		}
		else if (!empty($result["notification"])) {
			$message = $result["notification"];
		}
		else if (!empty($result["success"])) {
			update_option("ezfc_purchase_code", $_POST["purchase_code"]);
			update_option("ezfc_license_activated", 1);
			delete_transient("ezfc_template_list");

			$message = $result["success"];
		}
		else {
			$message = sprintf(__("Unknown error: %s", "ezfc"), var_export($result, true));
		}
	}
	// revoke license
	else if ($_POST["action"] == "revoke_license") {
		$result = Ezfc_Functions::revoke_license(get_option("ezfc_purchase_code"));

		if (!empty($result["error"])) {
			$message = $result["error"];
			$message_class = "notice notice-error";
		}
		else if (!empty($result["notification"])) {
			update_option("ezfc_purchase_code", "");
			update_option("ezfc_license_activated", 0);
			
			$message = $result["notification"];
		}
		else if (!empty($result["success"])) {
			update_option("ezfc_purchase_code", "");
			update_option("ezfc_license_activated", 0);

			$message = $result["success"];
		}
		else {
			$message = sprintf(__("Unknown error: %s", "ezfc"), var_export($result, true));
		}
	}
}

$license           = get_option("ezfc_license_activated", 0);
$license_activated = $license ? __("License registered.", "ezfc") : __("License not registered.", "ezfc");
$license_name      = get_bloginfo("name") . ", " . network_site_url( '/' );
$license_show_content = $license ? "license_active" : "license_inactive";

// check if localhost
if (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1') {
    //$license_show_content = "disabled";
}

$pc = get_option("ezfc_purchase_code", "");
if (!empty($pc)) {
	$obfuscated_string = substr($pc, 4, -4);
	if ($obfuscated_string) {
		$pc = str_replace( $obfuscated_string, str_repeat( '*', strlen( $obfuscated_string ) ), $pc );
	}
}

$nonce = wp_create_nonce("ezfc-nonce");

?>

<div class="ezfc wrap ezfc-wrapper container-fluid">
	<?php Ezfc_Functions::get_page_template_admin("header", __("Help / Debug", "ezfc"), $message); ?>

	<div class="row">
		<div class="col-lg-12">
			<div class="inner">
				<h2><?php echo __("Licensing", "ezfc"); ?> - ez Form Calculator v<?php echo EZFC_VERSION; ?></h2> 
				<p><?php echo __("In order to receive automatic updates, please register your purchase code here.", "ezfc"); ?></p>
				<p><?php echo __("Only one license per site is allowed. If you uninstall the plugin from a site, the license will be automatically removed from the site so you can register it somewhere else.", "ezfc"); ?></p>
				<p><?php echo sprintf(__("Please report any licensing problems to %s.", "ezfc"), "<a href='mailto:support@ezplugins.de'>support@ezplugins.de</a>"); ?></p>

				<?php if (!empty($message)) { ?>
					<div id="message" class="<?php echo $message_class; ?>"><?php echo $message; ?></div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="inner">
				<form action="" method="POST">
					<input type="hidden" name="ezfc-request" value="1" />
					
					<?php if ($license_show_content == "license_inactive") { ?>
						<p><?php echo __("Current licensing status:", "ezfc"); ?> <strong><?php echo $license_activated; ?></strong></p>

						<p>
							<?php echo __("The plugin will be licensed for:", "ezfc"); ?> <strong><?php echo $license_name; ?></strong>
						</p>

						<p>
							<label for="purchase_code"><?php echo __("Purchase code", "ezfc"); ?>:</label>
							<input type="text" name="purchase_code" id="purchase_code" required />
						</p>

						<p>
							<input type="checkbox" name="confirmation" id="confirmation" required />
							<label for="confirmation"><?php _e("I accept that my site address and purchase code will be stored as long as the purchase code remains valid to ensure license and copyright compliance. No other information will be stored.", "ezfc"); ?></label>
						</p>

						<input type="hidden" name="action" value="register_license" />
						<input type="submit" value="<?php echo __("Register license", "ezfc"); ?>" class="button button-primary" />
					<?php } else if ($license_show_content == "license_active") { ?>
						<p><?php echo __("Thank your for activating your copy of ez Form Calculator! :)", "ezfc"); ?></p>
						<p><?php echo __("Licensed for:", "ezfc"); ?> <strong><?php echo $license_name; ?></strong></p>

						<p><input type="text" value="<?php echo $pc; ?>" disabled style="width: 400px; "/></p>
						
						<input type="hidden" name="action" value="revoke_license" />
						<p><input type="submit" value="<?php echo __("Revoke license", "ezfc"); ?>" class="button button-primary" /></p>
					<?php } else if ($license_show_content == "disabled") { ?>
						<p><?php echo __("The plugin cannot be licensed on a local machine.", "ezfc"); ?></p>
					<?php } ?>

					<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
				</form>
			</div>
		</div>
	</div>
</div>
