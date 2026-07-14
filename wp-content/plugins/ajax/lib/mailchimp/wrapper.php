<?php

defined( 'ABSPATH' ) OR exit;

if (!class_exists("DrewM\MailChimp\MailChimp")) {
	require_once(EZFC_PATH . "lib/mailchimp/MailChimp.php");
}

use \DrewM\MailChimp\MailChimp;

abstract class Ezfc_Mailchimp_Wrapper {
	public static function get_instance($api_key) {	
		$mc = new MailChimp($api_key);
		return $mc;
	}
}