<?php

defined( 'ABSPATH' ) OR exit;

abstract class EZFC_WC_Hooks {
	static function init() {
		// woocommerce calculation
		add_action("woocommerce_before_calculate_totals", array(__CLASS__, "add_custom_price"));

		// show selected values on checkout page?
		if (get_option("ezfc_woocommerce_checkout_details") == 1) {
			add_filter("woocommerce_get_item_data", array(__CLASS__, "woo_add_item_data"), 10, 2);

			// checkout / email
			if (version_compare(WC_VERSION, "3.2.6") >= 0) {
				add_action("woocommerce_checkout_create_order_line_item", array(__CLASS__, "add_meta_line_item"), 10, 4 );
				add_action("woocommerce_new_order_item", array(__CLASS__, "woo_add_order_item_meta"), 10, 3);
			}
			else {
				add_action("woocommerce_add_order_item_meta", array(__CLASS__, "woo_add_order_item_meta"), 10, 3);
			}

			// remove unwanted details on checkout
			add_action("woocommerce_order_item_get_formatted_meta_data", array(__CLASS__, "woo_order_item_meta_format"), 10, 2);
		}

		// email
		add_action("woocommerce_checkout_order_processed", array(__CLASS__, "woo_send_mails"), 10, 2);
		// pdf
		//add_filter("woocommerce_email_attachments", array(__CLASS__, "woo_add_email_pdf"), 10, 2);

		// remove quantity (todo)
		//add_filter( 'woocommerce_is_sold_individually', '__return_true', 10, 2 );
	}

	static function add_meta_line_item($item, $cart_item_key, $values, $order) {
		if (empty($values["ezfc_form_id"])) return;

		$item->add_meta_data("ezfc_cart_product_key", $values["ezfc_cart_product_key"], true);
		$item->add_meta_data("ezfc_edit_values", $values["ezfc_edit_values"], true);
		$item->add_meta_data("ezfc_form_id", $values["ezfc_form_id"], true);
		$item->add_meta_data("ezfc_ref_id", $values["ezfc_ref_id"], true);
		$item->add_meta_data("ezfc_total", $values["ezfc_total"], true);
		$item->add_meta_data("ezfc_values", $values["ezfc_values"], true);
	}

	/**
		woocommerce custom price hook
	**/
	static function add_custom_price( $cart_object ) {
		foreach ( $cart_object->cart_contents as $key => $value ) {
			// do not mess with other products
			if (!isset($value["ezfc_total"])) continue;
			
			// change price
			$value["data"]->set_price($value["ezfc_total"]);
		}
	}

	/**
		woocommerce add cart item data
	**/
	static function woo_add_item_data( $cart_array, $cart_data ) {
		// do not mess with other products
		if (!isset($cart_data["ezfc_values"])) return $cart_array;

		// add edit link
		if (get_option("ezfc_woocommerce_enable_edit", 1)) {
			if (method_exists($cart_data["data"], "get_id")) {
				$product_link = get_permalink($cart_data["data"]->get_id());
			}
			else {
				$product_link = get_permalink($cart_data["data"]->id);
			}

			if (!empty($cart_data["ezfc_cart_product_key"])) {
				$edit_link = esc_url(add_query_arg("ezfc_cart_product_key", $cart_data["ezfc_cart_product_key"], $product_link));
			}
			// compatibility for old cart-items
			else {
				$edit_link = esc_url(add_query_arg("ezfc_edit_values", $cart_data["ezfc_edit_values"], $product_link));
			}

			$edit_text = get_option("ezfc_woocommerce_edit_text", __("Edit", "ezfc"));

			$cart_data["ezfc_values"]     .= "<br><a href='{$edit_link}'>{$edit_text}</a>";
			$cart_data["ezfc_raw_values"]  = $cart_data["ezfc_edit_values"];
		}
		
		return array(array(
			"name"  => get_option("ezfc_woocommerce_checkout_details_text"),
			"value" => $cart_data["ezfc_values"]
		));
	}

	/**
		woocommerce add item data to checkout / emails
	**/
	static function woo_add_order_item_meta($item_id, $values, $cart_item_key) {
		if (!empty($values["ezfc_values"])) {
			wc_add_order_item_meta($item_id, get_option("ezfc_woocommerce_checkout_details_text"), $values["ezfc_values"]);
			wc_add_order_item_meta($item_id, "ezfc_raw_values", $values["ezfc_edit_values"]);
			wc_add_order_item_meta($item_id, "ezfc_form_id", $values["ezfc_form_id"]);
		}
	}

	/**
		woocommerce send emails after checkout
	**/
	static function woo_send_mails($order_id) {
		$order = new WC_Order($order_id);

		if (!$order) return;

		require_once(EZFC_PATH . "class.ezfc_frontend.php");
		$ezfc = Ezfc_frontend::instance();

		$insert_db = get_option("ezfc_woocommerce_insert_db", 0);
		$send_mails = false;
		$send_mails_option = 0;
		$email_output = array(
			"admin" => "",
			"user"  => ""
		);

		$items = $order->get_items();
		foreach ($items as $item_id => $item_line) {
			// compat check
			if (version_compare(WC_VERSION, "3.2.6") >= 0) {
				$item = array(
					"ezfc_raw_values"       => wc_get_order_item_meta($item_id, "ezfc_raw_values", true),
					"ezfc_cart_product_key" => wc_get_order_item_meta($item_id, "ezfc_cart_product_key", true),
					"ezfc_edit_values"      => wc_get_order_item_meta($item_id, "ezfc_edit_values", true),
					"ezfc_form_id"          => wc_get_order_item_meta($item_id, "ezfc_form_id", true),
					"ezfc_ref_id"           => wc_get_order_item_meta($item_id, "ezfc_ref_id", true),
					"ezfc_total"            => wc_get_order_item_meta($item_id, "ezfc_total", true),
					"ezfc_values"           => wc_get_order_item_meta($item_id, "ezfc_values", true)
				);

				$raw_values = array("ezfc_element" => $item["ezfc_raw_values"]);
			}
			else {
				$item = $item_line;
				$raw_values = array("ezfc_element" => unserialize($item["ezfc_raw_values"]));
			}

			// do not mess with other products
			if (empty($item["ezfc_raw_values"]) || empty($item["ezfc_form_id"])) continue;

			$customer_mail   = "";
			$ref_id          = empty($item["ezfc_ref_id"]) ? "" : $item["ezfc_ref_id"];

			// prepare submission
			$ezfc->prepare_submission_data($item["ezfc_form_id"], $raw_values);
			$ezfc->replace_values["wc_order_id"] = $order_id;

			// insert to DB
			if ($insert_db) {
				$ret = $ezfc->insert($item["ezfc_form_id"], $item["ezfc_raw_values"], $ref_id, false, array(), true);
			}

			$send_mails_option = $ezfc->submission_data["options"]["woo_send_order_mails"];
			// no emails should be sent
			if (!$send_mails_option) continue;

			// set send mails flag
			$send_mails = true;

			// build output
			$tmp_output = $ezfc->get_mail_output($ezfc->submission_data);
			$email_output["admin"] .= $tmp_output["admin"] . "<br>";
			$email_output["user"]  .= $tmp_output["user"] . "<br>";
		}

		// send mails
		if ($send_mails) {
			$send_to_admin = $send_mails_option == "admin" || $send_mails_option == "both";
			// add customer email
			if ($send_mails_option == "both") $customer_mail = get_post_meta($order->get_id(), "_billing_email", true);

			$ezfc->send_mails(false, $email_output, $customer_mail, false, $ezfc->submission_data, array(
				"send_to_admin" => $send_to_admin
			));
		}
	}

	/**
		woocommerce add pdf to emails
	**/
	static function woo_add_email_pdf($attachments, $status) {
		// only for new orders
		if ($status != "new_order") return $attachments;

		// todo
		
		return $attachments;
	}

	/**
	 * unset ezfc_form_id meta
	 */
	static function woo_order_item_meta_format($formatted_meta, $order) {
		$keys = array("ezfc_cart_product_key", "ezfc_form_id", "ezfc_ref_id", "ezfc_total", "ezfc_values");
		$keys = apply_filters("ezfc_wc_item_meta_format", $keys, $order);

		foreach ($formatted_meta as $id => $meta) {
			if (in_array($meta->key, $keys)) {
				unset($formatted_meta[$id]);
			}
		}

		return $formatted_meta;
	}
}

EZFC_WC_Hooks::init();