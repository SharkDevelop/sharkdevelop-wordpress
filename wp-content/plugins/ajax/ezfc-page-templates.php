<?php

/**
	templates page
**/

defined( 'ABSPATH' ) OR exit;

require_once(EZFC_PATH . "class.ezfc_functions.php");
require_once(EZFC_PATH . "class.ezfc_backend.php");
$ezfc = Ezfc_backend::instance();

// validate user
if (!empty($_POST["ezfc-request"])) $ezfc->validate_user("ezfc-nonce", "nonce");

$nonce = wp_create_nonce("ezfc-nonce");
$message = array();
$templates = array();
$templates_browse = array();

// clear transients
if (!empty($_REQUEST["refresh_templates"])) {
	delete_transient("ezfc_template_list");

	$message[] = "Transients deleted.<br>";
}

$licensed = get_option("ezfc_license_activated", 0);
if (!$licensed) {
	$message[] = __("You need to register your license in order to install templates.", "ezfc");
	$tabs = array(
		__("No license", "ezfc")
	);
}
else {
	if (!empty($_POST["template_install_id"])) {
		$res = EZFC_Functions::template_install($_POST["template_install_id"]);
		$message[] = array_shift($res);
	}
	else if (!empty($_POST["template_submit_id"])) {
		$res = EZFC_Functions::template_submit($_POST);
		$message[] = array_shift($res);
	}

	$templates = $ezfc->form_templates_get();
	array_unshift($templates, json_decode(json_encode(array("id" => "-1", "name" => __("Please select a template", "ezfc")))));
	$templates_browse = EZFC_Functions::template_get_list();

	$tabs = array(
		__("Browse templates", "ezfc")
		//__("Submit template", "ezfc")
	);
}

?>

<div class="ezfc wrap ezfc-wrapper container-fluid">
	<?php Ezfc_Functions::get_page_template_admin("header", __("Form Templates", "ezfc"), $message); ?>

	<div class="row">
		<div class="col-lg-12">
			<div class="inner">
				<form method="POST" name="ezfc-form" class="ezfc-form" action="<?php echo admin_url('admin.php'); ?>?page=ezfc-templates" novalidate>
					<input type="hidden" name="ezfc-request" value="1" />
					<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
					<input type="hidden" name="template_install_id" />

					<?php
					if (EZFC_Functions::is_countable($templates_browse) && count($templates_browse) > 0 && $licensed) {
						foreach ($templates_browse as $i => $template) {
							$install_enabled = true;
							if (version_compare($template->version, EZFC_VERSION) > 0) {
								$install_enabled = false;
							}

							$install_link = admin_url("admin.php") . "?page=ezfc-templates&template_install_id={$template->id}";
							$preview_link = !empty($template->preview_link) ? $template->preview_link : "#";

							?>

							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<div class="ezfc-template-list-wrapper">
									<div class="ezfc-template-list-item-image" style="background-image: url(<?php echo $template->image_featured; ?>);"><a href="<?php echo $preview_link; ?>" target="_blank"></a></div>

									<h3 class="ezfc-template-list-name"><?php echo $template->name; ?></h3>

									<div class="ezfc-template-list-item-content">
										<div class="ezfc-template-list-item-description">
											<p><?php echo $template->description; ?></p>
										</div>

										<p class="ezfc-template-list-buttons">
											<a href="<?php echo $preview_link; ?>" class="button" target="_blank"><?php echo __("Preview", "ezfc"); ?></a>

											<?php if ($install_enabled) { ?>
												<input type="submit" name="install_template" data-id="<?php echo $template->id; ?>" value="<?php echo __("Install", "ezfc"); ?>" class="button button-primary ezfc-template-list-install" />
											<?php } else {
												echo "<br>" . sprintf(__("Requires version %s", "ezfc"), $template->version);
											} ?>
										</p>
									</div>
								</div>
							</div>

							<?php if (($i + 1)%4 == 0 && $i > 0) { ?>
								<div class="ezfc-clear"></div>
							<?php }
						}
					}
					else {
						?>

						<p>
							<?php echo __("No templates available.", "ezfc"); ?>
						</p>

						<p>
							<a href="<?php echo admin_url('admin.php') . '?page=ezfc-templates&refresh_templates=1'; ?>" class="button button-primary"><?php echo __("Refresh templates", "ezfc"); ?></a>
						</p>

						<?php
					}
					?>

					<div class="ezfc-clear"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
ezfc_nonce = "<?php echo $nonce; ?>";

jQuery(document).ready(function($) {
	$(".ezfc-template-list-install").click(function(e) {
		e.preventDefault();

		var id = $(this).data("id");
		var $form = $(this).closest("form");
		$form.find("[name='template_install_id']").val(id);
		$form.submit();

		return false;
	});

	$("#submit_template").click(function() {
		var id = $("#template_submit_id :selected").val();
		
		if (id == -1) return false;
	});
})
</script>