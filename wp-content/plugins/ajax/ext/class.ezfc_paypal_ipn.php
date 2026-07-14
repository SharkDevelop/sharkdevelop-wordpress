<?php

defined( 'ABSPATH' ) OR exit;

define("EZFC_EXT_PAYPAL_IPN_VERSION", "1.0.0");

abstract class EZFC_Extension_Paypal_IPN {
	/**
		constructor
	**/
	static function init() {
		// ipn listener
		add_action("init", array(__CLASS__, "paypal_add_rewrite_rule"));
		add_filter("query_vars", array(__CLASS__, "paypal_add_query_vars"));
		add_action("parse_request", array(__CLASS__, "paypal_parse_request"));
	}

	/**
		paypal ipn add rewrite rule
	**/
	static function paypal_add_rewrite_rule() {
		add_rewrite_rule("ezfc-pp-ipn\.php", "index.php?ezfc_paypal_api=1", "top");
	}

	/**
		paypal ipn add query var
	**/
	static function paypal_add_query_vars($query_vars) {
		$query_vars[] = "ezfc_paypal_api";
		return $query_vars;
	}

	/**
		paypal ipn parse request
	**/
	static function paypal_parse_request($query) {
		// load frontend
        require_once(EZFC_PATH . "class.ezfc_frontend.php");
        $ezfc = Ezfc_frontend::instance();

		if (!array_key_exists("ezfc_paypal_api", $query->query_vars)) return;

		$ezfc->debug("_POST vars:");
		$ezfc->debug(var_export($_POST, true));

		// load ipn listener
		require_once(EZFC_PATH . "lib/paypal/IpnListener.php");
		$listener = new \wadeshuler\paypalipn\IpnListener();

		if (get_option("ezfc_pp_sandbox", 0)) {
			$listener->use_sandbox = true;
		}
		$listener->verify_ssl = false;

		$verified = $listener->processIpn();
		if ($verified) {
			// check if txn ID exists
			if (empty($_POST["txn_id"])) {
				$ezfc->debug("IPN fail: Empty txn_id");
				die();
			}

			//$txn_id_exists = $ezfc->

			if ($_POST["payment_status"] == "Completed") {

			}
	        // 1. Check that $_POST['payment_status'] is "Completed"
	        // 2. Check that $_POST['txn_id'] has not been previously processed
	        // 3. Check that $_POST['receiver_email'] is your Primary PayPal email
	        // 4. Check that $_POST['payment_amount'] and $_POST['payment_currency'] are correct

		    // Valid IPN
		    $transactionRawData = $listener->getRawPostData();
		    $transactionData = $listener->getPostData();
		    $ezfc->debug("Valid IPN");
		    $ezfc->debug(var_export($transactionData, true));
		} else {
		    // Invalid IPN
		    $errors = $listener->getErrors();
		    $ezfc->debug("IPN error");
		    $ezfc->debug(var_export($errors, true));
		}

        die();
	}
}