<?php

abstract class Ezfc_settings {
	static $type_bool_text          = "bool_text";
	static $type_checkbox           = "checkbox";
	static $type_currencycodes      = "currencycodes";
	static $type_custom_calculation = "custom_calculation";
	static $type_dropdown           = "dropdown";
	static $type_editor             = "editor";
	static $type_email              = "email";
	static $type_file               = "file";
	static $type_input              = "input";
	static $type_input_multiple     = "input_multiple";
	static $type_numbers            = "numbers";
	static $type_password           = "password";
	static $type_radio              = "radio";
	static $type_repeatable_form    = "repeatable_form";
	static $type_table_order        = "table_order";
	static $type_textarea           = "textarea";
	static $type_template_themes    = "template_themes";
	static $type_yesno              = "yesno";

	static $calculate_array      = array(array("operator" => "", "target" => 0, "use_calculated_target_value" => 0, "value" => "", "prio" => 0));
	static $calculate_routine_array = array(array("name" => "", "index" => 0, "calculation_rows" => array(array("operator" => "", "target" => 0, "use_calculated_target_value" => 0, "value" => "", "prio" => 0))));
	static $conditional_array    = array(array("action" => "", "target" => 0, "operator" => "", "value" => ""));
	static $discount_array       = array(array("range_min" => "", "range_max" => "", "operator" => "", "discount_value" => ""));
	static $options_array        = array(array("id" => "", "value" => 0, "text" => "Option"));
	static $options_source_array = array("source" => "options",	"value"  => "");

	public static $elements;
	public static $elements_array = array();

	/**
		form elements
	**/
	static function get_elements() {
		self::$elements = array(
			array(
				"id" => 1,
				"name" => __("Input", "ezfc"),
				"description" => __("Basic input field with no restrictions", "ezfc"),
				"type" => self::$type_input,
				"data" => array(
					"name" => __("Input", "ezfc"),
					"label" => __("Text", "ezfc"),
					"required" => 0,
					"is_number" => 0,
					"is_currency" => 0,
					"conditional" => self::$conditional_array,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"text_before" => "",
					"text_after" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"is_telephone_nr" => 0,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"max_length" => "",
					"min_length" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-pencil-square-o",
				"category" => "basic"
			),
			array(
				"id" => 2,
				"name" => __("Email", "ezfc"),
				"description" => __("Email input field", "ezfc"),
				"type" => self::$type_email,
				"data" => array(
					"name" => __("Email", "ezfc"),
					"label" => __("Email", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"use_address" => 1,
					"double_check" => 0,
					"allow_multiple" => 0,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-envelope-o",
				"category" => "basic"
			),
			array(
				"id" => 3,
				"name" => __("Textfield", "ezfc"),
				"description" => __("Large text field", "ezfc"),
				"type" => "textfield",
				"data" => array(
					"name" => __("Textfield", "ezfc"),
					"label" => __("Textfield", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"max_length" => "",
					"min_length" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-align-justify",
				"category" => "basic"
			),
			array(
				"id" => 4,
				"name" => __("Dropdown", "ezfc"),
				"description" => __("Dropdown list", "ezfc"),
				"type" => self::$type_dropdown,
				"data" => array(
					"name" => __("Dropdown", "ezfc"),
					"label" => __("Dropdown", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"options" => self::$options_array,
					"options_source" => self::$options_source_array,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"GET_check_option_value" => "index",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-list-ul",
				"category" => "calc"
			),
			array(
				"id" => 5,
				"name" => __("Radio Button", "ezfc"),
				"description" => __("Used for single-choice elements.", "ezfc"),
				"type" => self::$type_radio,
				"data" => array(
					"name" => __("Radio", "ezfc"),
					"label" => __("Radio", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"options" => self::$options_array,
					"options_columns" => "",
					//"options_connected" => array("target" => ""),
					"options_source" => self::$options_source_array,
					"options_text_only" => 0,
					"option_add_text_icon" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"max_width" => "",
					"max_height" => "",
					"image_auto_width" => 0,
					"inline" => 0,
					"flexbox" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"GET_check_option_value" => "index",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-dot-circle-o",
				"category" => "calc"
			),
			array(
				"id" => 6,
				"name" => __("Checkbox", "ezfc"),
				"description" => __("Checky check!", "ezfc"),
				"type" => self::$type_checkbox,
				"data" => array(
					"name" => __("Checkbox", "ezfc"),
					"label" => __("Checkbox", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"min_selectable" => 0,
					"max_selectable" => 0,
					"options" => self::$options_array,
					"options_columns" => "",
					"options_source" => self::$options_source_array,
					"options_text_only" => 0,
					"option_add_text_icon" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"max_width" => "",
					"max_height" => "",
					"image_auto_width" => 0,
					"inline" => 0,
					"flexbox" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"GET_check_option_value" => "index",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-check-circle-o",
				"category" => "calc"
			),
			array(
				"id" => 7,
				"name" => __("Numbers", "ezfc"),
				"description" => __("Numbers only", "ezfc"),
				"type" => self::$type_numbers,
				"data" => array(
					"name" => __("Numbers", "ezfc"),
					"label" => __("Numbers", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"factor" => "",
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"min" => "",
					"max" => "",
					"slider" => 0,
					"slider_values" => "",
					"steps_slider" => 1,
					"slider_vertical" => 0,
					"spinner" => 0,
					"steps_spinner" => 1,
					"pips" => 0,
					"steps_pips" => 1,
					"pips_float" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"price_format" => "",
					"precision" => 2,
					"text_only" => 0,
					"text_before" => "",
					"text_after" => "",
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"max_length" => "",
					"min_length" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"image" => "",
					"alt" => "",
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-html5",
				"category" => "calc"
			),
			array(
				"id" => 8,
				"name" => __("Date", "ezfc"),
				"description" => __("Datepicker", "ezfc"),
				"type" => "datepicker",
				"data" => array(
					"name" => __("Datepicker", "ezfc"),
					"label" => __("Datepicker", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => "1",
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"minDate" => "",
					"maxDate" => "",
					"numberOfMonths" => "1",
					"datepicker_change_month" => 0,
					"datepicker_change_year" => 0,
					"datepicker_year_range" => "",
					"showAnim" => "fadeIn",
					"showWeek" => "0",
					"firstDay" => "1",
					"available_days" => "0,1,2,3,4,5,6",
					"blocked_days" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-calendar",
				"category" => "basic"
			),
			array(
				"id" => 9,
				"name" => __("Image", "ezfc"),
				"description" => __("Shows images", "ezfc"),
				"type" => "image",
				"data" => array(
					"name" => __("Image", "ezfc"),
					"image" => "",
					"alt" => "",
					"featured_image" => "",
					"featured_image_size" => "",
					"fallback_image" => "",
					"show_in_email" => 0,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-picture-o",
				"category" => "other"
			),
			array(
				"id" => 10,
				"name" => __("Hidden", "ezfc"),
				"description" => __("Hidden input field", "ezfc"),
				"type" => "hidden",
				"data" => array(
					"name" => __("Hidden", "ezfc"),
					"label" => __("Hidden", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"precision" => 2,
					"calculate_when_hidden" => 1,
					"factor" => "", 
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"use_woocommerce_price" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"max_length" => "",
					"min_length" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-square-o",
				"category" => "calc"
			),
			array(
				"id" => 11,
				"name" => __("Line", "ezfc"),
				"description" => __("Horizontal line", "ezfc"),
				"type" => "hr",
				"data" => array(
					"name" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-minus",
				"category" => "other"
			),
			array(
				"id" => 12,
				"name" => __("HTML", "ezfc"),
				"description" => __("Custom HTML or basic text", "ezfc"),
				"type" => "html",
				"data" => array(
					"name" => __("HTML", "ezfc"),
					"label" => "",
					"do_shortcode" => 1,
					"html" => "",
					"add_linebreaks" => 1,
					"show_in_email" => 0,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"replace_placeholders" => 1,
					"replace_placeholders_show_zero" => 1,
					"wrapper_class" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-code",
				"category" => "other"
			),
			array(
				"id" => 13,
				"name" => __("Recaptcha", "ezfc"),
				"description" => __("Recaptcha", "ezfc"),
				"type" => "recaptcha",
				"data" => array(
					"name" => __("Verification", "ezfc"),
					"label" => __("Verification", "ezfc"),
					"required" => 1,
					"verify_value" => 1,
					"recaptcha_language" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-lock",
				"category" => "other"
			),
			array(
				"id" => 14,
				"name" => __("File upload", "ezfc"),
				"description" => __("File upload", "ezfc"),
				"type" => "fileupload",
				"data" => array(
					"name" => __("File upload", "ezfc"),
					"label" => __("File upload", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"multiple" => 0,
					"file_upload_auto" => 1,
					"placeholder" => "",
					"class" => "",
					"wrapper_class" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-file-o",
				"category" => "other"
			),
			array(
				"id" => 15,
				"name" => __("Subtotal", "ezfc"),
				"description" => __("This element holds the subtotal value up to the point where the element is placed at. It can also be used to perform custom calculations to override or modify the price at any point.", "ezfc"),
				"type" => "subtotal",
				"data" => array(
					"name" => __("Subtotal", "ezfc"),
					"label" => __("Subtotal", "ezfc"),
					"calculate_enabled" => 1,
					"add_to_price" => 2,
					"is_currency" => 1,
					"is_number" => 1,
					"min" => "",
					"max" => "",
					"overwrite_price" => 1,
					"calculate_before" => 0,
					"calculate_when_hidden" => 0,
					"price_format" => "",
					"precision" => 2,
					"text_only" => 0,
					"text_before" => "",
					"text_after" => "",
					"read_only" => 0,
					"inline_calculation" => "",
					//"inline_expression" => "",
					"calculate_when_zero" => 1,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-thumb-tack",
				"category" => "calc"
			),
			array(
				"id" => 16,
				"name" => __("Payment", "ezfc"),
				"description" => __("User can choose the payment type", "ezfc"),
				"type" => "payment",
				"data" => array(
					"name" => __("Payment type", "ezfc"),
					"label" => __("Payment type", "ezfc"),
					"required" => 1,
					"verify_value" => 1,
					"is_currency" => 0,
					"is_number" => 0,
					"options" => array(
						array("value" => "bank", "text" => "Bank transfer"),
						array("value" => "cash", "text" => "Cash on delivery"),
						array("value" => "paypal", "text" => "PayPal"),
						array("value" => "stripe", "text" => "Stripe")
					),
					"options_source" => self::$options_source_array,
					"options_text_only" => 0,
					"option_add_text_icon" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"inline" => 0,
					"flexbox" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"GET_check_option_value" => "index",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-usd",
				"category" => "other"
			),
			array(
				"id" => 17,
				"name" => __("Timepicker", "ezfc"),
				"description" => __("Timepicker", "ezfc"),
				"type" => "timepicker",
				"data" => array(
					"name" => __("Timepicker", "ezfc"),
					"label" => __("Timepicker", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"placeholder" => "",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"format" => "",
					"minTime" => "",
					"maxTime" => "",
					"steps" => "30",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-clock-o",
				"category" => "basic"
			),
			array(
				"id" => 18,
				"name" => __("Step start", "ezfc"),
				"description" => __("Divide form into steps - Start of a step", "ezfc"),
				"type" => "stepstart",
				"data" => array(
					"name" => __("Step", "ezfc"),
					"title" => "",
					"class" => "",
					"wrapper_class" => "",
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-caret-square-o-down",
				"category" => "steps"
			),
			array(
				"id" => 19,
				"name" => __("Step end", "ezfc"),
				"description" => __("Divide form into steps - End of a step", "ezfc"),
				"type" => "stepend",
				"data" => array(
					"name" => __("Step end", "ezfc"),
					"previous_step" => "&larr; " . __("Previous Step", "ezfc"),
					"next_step" => __("Next Step", "ezfc") . " &rarr;",
					"add_line" => "1",
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-caret-square-o-up",
				"category" => "steps"
			),
			array(
				"id" => 20,
				"name" => __("Date range", "ezfc"),
				"description" => __("Use this element when you want to calculate a date range (e.g. number of days)", "ezfc"),
				"type" => "daterange",
				"data" => array(
					"name" => __("Date range", "ezfc"),
					"label" => __("Date range", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"value" => "",
					"factor" => "",
					"minDate" => "+1d;;+2d",
					"maxDate" => "",
					"minDays" => 1,
					"maxDays" => 0,
					"workdays_only" => 0,
					"numberOfMonths" => "1",
					"datepicker_day_prices_default" => "",
					"datepicker_day_prices" => "",
					"datepicker_change_month" => 0,
					"datepicker_change_year" => 0,
					"datepicker_year_range" => "c-10:c+10",
					"showAnim" => "fadeIn",
					"showWeek" => "0",
					"firstDay" => "1",
					"available_days" => "0,1,2,3,4,5,6",
					"blocked_days" => "",
					"daterange_single" => 0,
					"daterange_count_full_days" => 0,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"read_only" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"placeholder" => "From;;To",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-calendar",
				"category" => "calc"
			),
			array(
				"id" => 21,
				"name" => __("Colorpicker", "ezfc"),
				"description" => __("Colorpicker", "ezfc"),
				"type" => "colorpicker",
				"data" => array(
					"name" => __("Colorpicker", "ezfc"),
					"label" => __("Pick your color", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-eyedropper",
				"category" => "basic"
			),
			array(
				"id" => 22,
				"name" => __("Set", "ezfc"),
				"description" => __("Apply a math operator to a set of elements (e.g. choose lowest / highest value, average, etc.)", "ezfc"),
				"type" => "set",
				"data" => array(
					"name" => __("Set", "ezfc"),
					"label" => __("Set", "ezfc"),
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"overwrite_price" => 1,
					"calculate_when_hidden" => 0,
					"price_format" => "",
					"precision" => 2,
					"text_only" => 0,
					"text_before" => "",
					"text_after" => "",
					"set_operator" => "min",
					"set_use_factor" => 1,
					"set_allow_zero" => 1,
					"set_dom_selector" => "",
					"set" => array("target" => "" ),
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-bars",
				"category" => "calc"
			),
			array(
				"id" => 23,
				"name" => __("Post", "ezfc"),
				"description" => __("Show the content of a WordPress post.", "ezfc"),
				"type" => "post",
				"data" => array(
					"name" => __("Post", "ezfc"),
					"label" => __("Post", "ezfc"),
					"post_id" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-thumb-tack",
				"category" => "other"
			),
			array(
				"id" => 24,
				"name" => __("Custom JS", "ezfc"),
				"description" => __("Custom JavaScript code. Uses price as argument and returns the price (no need to add function name or value).", "ezfc"),
				"type" => self::$type_custom_calculation,
				"data" => array(
					"name" => __("Custom Calculation", "ezfc"),
					"label" => __("Custom Calculation", "ezfc"),
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"calculate_when_hidden" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"overwrite_price" => 1,
					"precision" => 2,
					"text_only" => 0,
					"text_before" => "",
					"text_after" => "",
					"custom_calculation" => "",
					"custom_calculation_function_name" => "",
					"custom_calculation_function_safe" => 1,
					"custom_calculation_function_external" => "",
					"custom_calculation_init_add" => 1,
					"custom_calculation_init_js" => "",
					"custom_calculation_function_read_file" => 0,
					"custom_calculation_function_filename" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-calculator",
				"category" => "calc"
			),
			array(
				"id" => 25,
				"name" => __("Group", "ezfc"),
				"description" => __("Group", "ezfc"),
				"type" => "group",
				"data" => array(
					"name" => __("Group", "ezfc"),
					"collapsible" => 0,
					"expanded" => 1,
					"title" => __("Collapse title", "ezfc"),
					//"repeatable" => 0,
					"group_center" => 0,
					"group_flexbox" => 0,
					"group_flexbox_align_items" => "center",
					"group_flexbox_columns" => "",
					"wrapper_class" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-folder",
				"category" => "other"
			),
			array(
				"id" => 26,
				"name" => __("Placeholder", "ezfc"),
				"description" => __("Placeholders have no effect on the form calculation.", "ezfc"),
				"type" => "placeholder",
				"data" => array(
					"name" => __("Placeholder", "ezfc"),
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-square",
				"category" => "other"
			),
			array(
				"id" => 27,
				"name" => __("Spacer", "ezfc"),
				"description" => __("Add vertical spacing.", "ezfc"),
				"type" => "spacer",
				"data" => array(
					"name" => __("Spacer", "ezfc"),
					"height" => 30,
					"wrapper_class" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"style" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-arrows-v",
				"category" => "other"
			),
			array(
				"id" => 28,
				"name" => __("Tax", "ezfc"),
				"description" => __("Adds tax to the total price. This element should be placed at the end of the form as it takes the subtotal value into calculation. Tax value can be changed in the calculation section.", "ezfc"),
				"type" => "subtotal",
				"data" => array(
					"name" => __("Tax (20%)", "ezfc"),
					"label" => __("Tax (20%)", "ezfc"),
					"calculate_enabled" => 1,
					"add_to_price" => 2,
					"is_currency" => 1,
					"is_number" => 1,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"price_format" => "",
					"precision" => 2,
					"text_only" => 1,
					"text_before" => "",
					"text_after" => "",
					"calculate" => array(
						array("operator" => "subtotal", "target" => 0, "value" => 0),
						array("operator" => "multiply", "target" => 0, "value" => 0.2)
					),
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-percent",
				"category" => "predefined"
			),
			array(
				"id" => 29,
				"name" => __("Quantity discount", "ezfc"),
				"description" => __("Numbers element with quantity discount. Please note that any factor entered will not be taken into calculations, so the 'raw' value will always be used.", "ezfc"),
				"type" => self::$type_numbers,
				"data" => array(
					"name" => __("Quantity Discount", "ezfc"),
					"label" => __("Quantity Discount", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"factor" => "",
					"value" => "",
					"value_attribute" => "",
					"value_external" => "",
					"value_external_listen" => 1,
					"value_http" => "",
					"value_http_json" => "",
					"min" => "",
					"max" => "",
					"slider" => 0,
					"slider_values" => "",
					"steps_slider" => 1,
					"slider_vertical" => 0,
					"spinner" => 0,
					"steps_spinner" => 1,
					"pips" => 0,
					"steps_pips" => 1,
					"pips_float" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount" => array(
						array("range_min" => "", "range_max" => "9", "operator" => "percent_sub", "discount_value" => "10"),
						array("range_min" => "10", "range_max" => "19", "operator" => "percent_sub", "discount_value" => "25"),
						array("range_min" => "20", "range_max" => "", "operator" => "percent_sub", "discount_value" => "50"),
					),
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"read_only" => 0,
					"placeholder" => "",
					"icon" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-percent",
				"category" => "predefined"
			),
			array(
				"id" => 30,
				"name" => __("Add to email list", "ezfc"),
				"description" => __("This checkbox element asks the user to sign up for an email list (MailChimp or MailPoet). Do not change the value of the checkbox or else the user will not be added to the list. Make sure to set 'Enable Mailchimp' or 'Enable Mailpoet' to 'Yes' in the form options.", "ezfc"),
				"type" => self::$type_checkbox,
				"data" => array(
					"name" => __("Newsletter", "ezfc"),
					"label" => __("Newsletter", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 0,
					"add_to_price" => 0,
					"is_currency" => 0,
					"is_number" => 0,
					"options" => array(array("value" => "__newsletter_signup__", "text" => __("Sign me up!", "ezfc"))),
					"options_source" => self::$options_source_array,
					"options_text_only" => 0,
					"option_add_text_icon" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 2,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"inline" => 0,
					"flexbox" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-check-circle-o",
				"category" => "predefined"
			),
			array(
				"id" => 31,
				"name" => __("Heading", "ezfc"),
				"description" => __("This element adds an HTML heading tag (h1-h6).", "ezfc"),
				"type" => "heading",
				"data" => array(
					"name" => __("Heading", "ezfc"),
					"title" => __("Title", "ezfc"),
					"tag" => "h2",
					"show_in_email" => 0,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"alignment" => "default",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-header",
				"category" => "other"
			),
			array(
				"id" => 32,
				"name" => __("Star Rating", "ezfc"),
				"description" => __("Simple star rating for rating purposes", "ezfc"),
				"type" => "starrating",
				"data" => array(
					"name" => __("Rating", "ezfc"),
					"label" => __("How would you rate us?", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => 0,
					"stars" => 5,
					"half_stars" => 0,
					"conditional" => self::$conditional_array,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-star",
				"category" => "other"
			),
			array(
				"id" => 33,
				"name" => __("Confirmation", "ezfc"),
				"description" => __("This checkbox element asks the user to confirm their submission.", "ezfc"),
				"type" => self::$type_checkbox,
				"data" => array(
					"name" => __("Confirmation", "ezfc"),
					"label" => __("Confirmation", "ezfc"),
					"required" => 1,
					"verify_value" => 1,
					"calculate_enabled" => 0,
					"add_to_price" => 0,
					"is_currency" => 0,
					"is_number" => 0,
					"options" => array(array("value" => "1", "text" => __("I agree that the data entered in the form may be processed and used solely for submitting information and advertising on products, services and other activities to my hand.", "ezfc"))),
					"options_source" => self::$options_source_array,
					"options_text_only" => 0,
					"option_add_text_icon" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"inline" => 0,
					"flexbox" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-check-circle-o",
				"category" => "predefined"
			),
			array(
				"id" => 34,
				"name" => __("Matrix", "ezfc"),
				"description" => __("Set element values conditionally in a user-friendly matrix layout", "ezfc"),
				"type" => "matrix",
				"data" => array(
					"name" => __("Matrix", "ezfc"),
					"label" => __("Matrix", "ezfc"),
					"matrix_action" => "set",
					"matrix" => array(
						"conditions" => array(
							array(
								"elements"  => array(0),
								"operators" => array(0),
								"values"    => array(""),
							)
						),
						"target_elements" => array(0),
						"target_values"   => array(
							array("")
						)
					),
					"hidden" => 1,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-table",
				"category" => "calc"
			),
			array(
				"id" => 35,
				"name" => __("Table Order", "ezfc"),
				"description" => __("Table-like list for item quantity orders", "ezfc"),
				"type" => self::$type_table_order,
				"data" => array(
					"name" => __("Table order", "ezfc"),
					"label" => __("Table order", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"options" => self::$options_array,
					"options_source" => self::$options_source_array,
					"do_shortcode" => 0,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"steps_spinner" => 1,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"table_order_loop_function" => "",
					"table_order_add_header" => 0,
					"table_order_header_text" => __("Product|Price per Unit|Quantity|Subtotal", "ezfc"),
					"table_order_add_footer" => 0,
					"table_order_footer_text" => __("Price", "ezfc"),
					"show_item_price" => 1,
					"show_subtotal_column" => 1,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"show_empty_values_in_email" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-th",
				"category" => "calc"
			),
			array(
				"id" => 36,
				"name" => __("Password", "ezfc"),
				"description" => __("Password field with masked characters.", "ezfc"),
				"type" => self::$type_password,
				"data" => array(
					"name" => __("Password", "ezfc"),
					"label" => __("Password", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"value" => "",
					"value_attribute" => "",
					"placeholder" => "",
					"icon" => "",
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"max_length" => "",
					"min_length" => "",
					"store_type" => array(
						"type" => "none",
						"func" => "",
					),
					"double_check" => 0,
					"show_in_email" => 0,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 0,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"autocomplete" => 1,
					"read_only" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-lock",
				"category" => "basic"
			),
			array(
				"id" => 37,
				"name" => __("Yes/No", "ezfc"),
				"description" => __("Simple yes/no single choice radio button.", "ezfc"),
				"type" => self::$type_radio,
				"data" => array(
					"name" => __("Yes/No", "ezfc"),
					"label" => __("Yes/No", "ezfc"),
					"required" => 0,
					"verify_value" => 1,
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"is_currency" => 1,
					"is_number" => 1,
					"options" => array(
						array("id" => "yes", "value" => "", "text" => __("Yes", "ezfc"), "icon" => "fa-check"),
						array("id" => "no", "value" => "", "text" => __("No", "ezfc"), "icon" => "fa-times"),
					),
					"options_source" => self::$options_source_array,
					"options_text_only" => 1,
					"option_add_text_icon" => 1,
					"calculate" => self::$calculate_array,
					"calculate_routine" => self::$calculate_routine_array,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"precision" => 2,
					"calculate_before" => 0,
					"conditional" => self::$conditional_array,
					"discount_value_type" => "raw",
					"discount_value_apply" => "calculated",
					"discount" => self::$discount_array,
					"discount_show_table" => 0,
					"discount_show_table_indicator" => 1,
					"custom_regex" => "",
					"custom_error_message" => "",
					"custom_regex_check_first" => 0,
					"custom_filter" => "",
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"description" => "",
					"description_below_label" => "",
					"description_below_input" => "",
					"max_width" => "",
					"max_height" => "",
					"image_auto_width" => 1,
					"inline" => 1,
					"flexbox" => 0,
					"class" => "",
					"wrapper_class" => "",
					"style" => "",
					"wrapper_style" => "",
					"element_style" => "default",
					"GET" => "",
					"GET_check_option_value" => "index",
					"hidden" => 0,
					"keep_value_after_reset" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-dot-circle-o",
				"category" => "predefined"
			),
			array(
				"id" => 38,
				"name" => __("Repeatable Form", "ezfc"),
				"description" => __("Repeatable Form", "ezfc"),
				"type" => self::$type_repeatable_form,
				"data" => array(
					"name" => __("Repeatable Form", "ezfc"),
					"label" => "",
					"repeatable" => 1,
					"repeatable_form_id" => 0,
					"repeatable_count_start" => 0,
					"repeatable_count_max" => 0,
					"repeat_button_text" => __("Add row", "ezfc"),
					"remove_button_text" => __("Remove row", "ezfc"),
					"calculate_enabled" => 1,
					"add_to_price" => 1,
					"overwrite_price" => 0,
					"calculate_when_hidden" => 0,
					"is_currency" => 1,
					"is_number" => 1,
					"show_in_email" => 1,
					"show_in_email_label" => "name",
					"show_in_email_cond" => 0,
					"show_in_email_operator" => 0,
					"show_in_email_value" => "",
					"email_text_column_1" => "submission_value",
					"email_text_column_2" => "calculated_value",
					"show_in_live_summary" => 1,
					"hidden" => 0,
					"columns" => 6,
					"group_id" => 0
				),
				"icon" => "fa-list-alt",
				"category" => "calc"
			),
		);

		// map to ID array
		self::$elements_array = array();
		foreach (self::$elements as $element) {
			self::$elements_array[$element["id"]] = $element;
		}

		return json_decode(json_encode(self::$elements));
	}

	/**
		global settings
	**/
	static function get_global_settings($flat = false) {
		require_once(EZFC_PATH . "inc/php/settings/global-settings.php");

		$settings = ezfc_get_global_settings();
		$settings = apply_filters("ezfc_global_settings", $settings);

		// get values
		foreach ($settings as $cat => &$settings_cat) {
			foreach ($settings_cat as $name => &$setting) {
				$default = isset($setting["default"]) ? $setting["default"] : "";

				$setting["value"] = get_option("ezfc_{$name}", $default);
			}
		}

		if ($flat) {
			$settings = self::flatten($settings);
		}

		return $settings;
	}

	/**
		update global settings
	**/
	public static function update_global_settings($submitted_values) {
		$settings = self::get_global_settings(true);

		// css array builder
		$css_builder = new EZ_CSS_Builder(".ezfc-wrapper");

		$return_message = array();

		foreach ($settings as $setting_key => $setting) {
			if (!isset($submitted_values[$setting_key])) {
				// set value for empty bool settings
				if ($setting["type"] == "yesno") {
					$submitted_values[$setting_key] = 0;
				}
				// skip if not set
				else continue;
			}

			// get post value
			$value = $submitted_values[$setting_key];

			if (is_array($value)) {
				$value = serialize($value);
			}
			else {
				$value = self::validate_option($setting, $value, $setting_key);

				if (is_array($value) && !empty($value["error"])) {
					$return_message[] = array("error" => $value["error"], "id" => $setting_key);
					continue;
				}
			}

			// update wp option
			update_option("ezfc_{$setting_key}", $value);

			// check for css
			if (!empty($setting["css"]) && !empty($value)) {
				$css_builder->add_css($setting["css"], $value);
			}
		}

		// build css output
		$css_output = $css_builder->get_output();
		update_option("ezfc_css_custom_styling", $css_output);

		do_action("ezfc_global_settings_after_update", $submitted_values);

		return $return_message;
	}

	/**
		form options
	**/
	static function get_form_options($flat = false, $global = false) {
		require_once(EZFC_PATH . "inc/php/settings/form-options.php");

		$settings = ezfc_get_form_options($global);
		$settings = apply_filters("ezfc_form_options", $settings, $global);

		if ($flat) {
			$settings = self::flatten($settings);
		}

		return $settings;
	}

	/**
		prepare form elements for export, e.g. replace target IDs with positions
	**/
	static function form_elements_prepare_export($form_elements = array()) {
		// replace calculate positions with target ids
		$template_elements_indexed = self::array_index_key($form_elements, "id");

		foreach ($template_elements_indexed as $id => &$element) {
			$element->id = $element->position;

			if (!property_exists($element, "data")) continue;

			$element_data = json_decode($element->data);

			// calculate elements
			if (property_exists($element_data, "calculate") &&
				!empty($element_data->calculate) &&
				Ezfc_Functions::is_countable($element_data->calculate) &&
				count($element_data->calculate) > 0) {

				// convert object to array
				if (!is_array($element_data->calculate)) {
					$element_data->calculate = (array) $element_data->calculate;
				}

				foreach ($element_data->calculate as &$calc_value) {
					if (empty($calc_value->target)) continue;

					if (!isset($template_elements_indexed[$calc_value->target])) continue;

					$target_element = $template_elements_indexed[$calc_value->target];
					$calc_id = $target_element->position;

					$calc_value->target = $calc_id;
				}
			}

			// conditional elements
			if (property_exists($element_data, "conditional") &&
				!empty($element_data->conditional) &&
				Ezfc_Functions::is_countable($element_data->conditional) &&
				count($element_data->conditional) > 0) {

				$element_data->conditional = self::prepare_export_conditional($element_data->conditional, $template_elements_indexed);
			}

			// set elements
			if (property_exists($element_data, "set") &&
				!empty($element_data->set) &&
				Ezfc_Functions::is_countable($element_data->set) &&
				count($element_data->set) > 0) {
				// convert object to array
				if (!is_array($element_data->set)) {
					$element_data->set = (array) $element_data->set;
				}

				foreach ($element_data->set as &$set_element) {
					if (empty($set_element->target)) continue;

					if (!isset($template_elements_indexed[$set_element->target])) continue;

					$target_element = $template_elements_indexed[$set_element->target];
					$cond_id = $target_element->position;

					$set_element->target = $cond_id;
				}
			}

			// groups
			if (!empty($element_data->group_id)) {
				if (isset($template_elements_indexed[$element_data->group_id])) {
					$target_element = $template_elements_indexed[$element_data->group_id];
					$target_id      = $target_element->position;

					$element_data->group_id = $target_id;
				}
			}

			// show in email target
			if (!empty($element_data->show_in_email_cond) && is_array($element_data->show_in_email_cond)) {
				foreach ($element_data->show_in_email_cond as $i => $cond) {
					if (!isset($template_elements_indexed[$cond])) continue;
				
					$target_element = $template_elements_indexed[$cond];
					$target_id      = $target_element->position;
					$element_data->show_in_email_cond[$i] = $target_id;
				}
			}

			// matrix
			if (!empty($element_data->matrix)) {
				// target elements
				if (!empty($element_data->matrix->target_elements)) {
					foreach ($element_data->matrix->target_elements as $i => $target_element_matrix) {
						if (!isset($template_elements_indexed[$target_element_matrix])) continue;

						$target_element = $template_elements_indexed[$target_element_matrix];
						$target_id      = $target_element->position;
						$element_data->matrix->target_elements[$i] = $target_id;
					}
				}

				// matrix conditions
				if (!empty($element_data->matrix->conditions)) {
					foreach ($element_data->matrix->conditions as $i => $matrix_condition) {
						if (empty($matrix_condition->elements) || !is_array($matrix_condition->elements)) continue;
						
						foreach ($matrix_condition->elements as $mi => $matrix_condition_element) {
							if (!isset($template_elements_indexed[$matrix_condition_element])) continue;
							
							$target_element = $template_elements_indexed[$matrix_condition_element];
							$target_id      = $target_element->position;
							$element_data->matrix->conditions[$i]->elements[$mi] = $target_id;
						}
					}
				}
			}

			$element->data = json_encode($element_data);
		}

		return $template_elements_indexed;
	}

	static function prepare_export_conditional($conditional_array, $template_elements_indexed) {
		// convert object to array
		if (!is_array($conditional_array)) {
			$conditional_array = (array) $conditional_array;
		}

		foreach ($conditional_array as &$cond_value) {
			if (empty($cond_value->target)) continue;

			if (!isset($template_elements_indexed[$cond_value->target])) continue;

			$target_element = $template_elements_indexed[$cond_value->target];
			$cond_id = $target_element->position;
			$cond_value->target = $cond_id;

			// compare value first
			if (property_exists($cond_value, "compare_value_first") && isset($template_elements_indexed[$cond_value->compare_value_first])) {
				$cond_value->compare_value_first = $template_elements_indexed[$cond_value->compare_value_first]->position;
			}

			// chain target
			if (property_exists($cond_value, "compare_value") && is_array($cond_value->compare_value) && Ezfc_Functions::is_countable($cond_value->compare_value) && count($cond_value->compare_value) > 0) {
				foreach ($cond_value->compare_value as &$chain_target_id) {
					if (isset($template_elements_indexed[$chain_target_id])) {
						$chain_target_id = $template_elements_indexed[$chain_target_id]->position;
					}
				}
			}
		}

		return $conditional_array;
	}

	static function prepare_import_conditional($conditional_array, $template_elements_indexed, $is_global = false) {
		// convert object to array
		if (!is_array($conditional_array)) {
			$conditional_array = (array) $conditional_array;
		}

		foreach ($conditional_array as &$cond_value) {
			if (empty($cond_value->target)) continue;

			if (!isset($template_elements_indexed[$cond_value->target])) continue;

			$target_element = $template_elements_indexed[$cond_value->target];
			$cond_id = $target_element->id;
			$cond_value->target = $cond_id;

			// compare value first
			if (property_exists($cond_value, "compare_value_first") && isset($template_elements_indexed[$cond_value->compare_value_first])) {
				$cond_value->compare_value_first = $template_elements_indexed[$cond_value->compare_value_first]->id;
			}

			// chain target
			if (property_exists($cond_value, "compare_value") && is_array($cond_value->compare_value) && count($cond_value->compare_value) > 0) {
				foreach ($cond_value->compare_value as &$chain_target_id) {
					if (isset($template_elements_indexed[$chain_target_id])) {
						$chain_target_id = $template_elements_indexed[$chain_target_id]->id;
					}
				}
			}
		}

		return $conditional_array;
	}

	static function flatten($settings) {
		$settings_flat = array();

		foreach ($settings as $cat => $settings_cat) {
			foreach ($settings_cat as $name => $setting) {
				$tmp_id = "";
				
				if (is_array($setting)) {
					if (!empty($setting["id"])) $tmp_id = $setting["id"];
					else if (!empty($setting["name"])) $tmp_id = $setting["name"];
					else $tmp_id = $name;
				}
				else if (is_object($setting)) {
					if (!empty($setting->id)) $tmp_id = $setting->id;
					else if (!empty($setting->name)) $tmp_id = $setting->name;
					else $tmp_id = $name;
				}

				$settings_flat[$tmp_id] = $setting;
			}
		}

		return $settings_flat;
	}

	// wrapper for deprecated extensions / customizations
	public static function array_index_key($array, $key) {
		return Ezfc_Functions::array_index_key($array, $key);
	}

	public static function get_conditional_operators() {
		return array(
			"0" => " ",
			"gr" => ">",
			"gre" => ">=",
			"less" => "<",
			"lesse" => "<=",
			"equals" => "=",
			"not" => __("not", "ezfc"),
			"between" => __("between", "ezfc"),
			"not_between" => __("not between", "ezfc"),
			"hidden" => __("is hidden", "ezfc"),
			"visible" => __("is visible", "ezfc"),
			"mod0" => __("%x = 0", "ezfc"),
			"mod1" => __("%x != 0", "ezfc"),
			"bit_and" => __("bitwise AND", "ezfc"),
			"bit_or" => __("bitwise OR", "ezfc"),
			"empty" => __("empty", "ezfc"),
			"notempty" => __("not empty", "ezfc"),
			"in" => __("in", "ezfc"),
			"not_in" => __("not in", "ezfc"),
			"once" => __("once", "ezfc")
		);
	}

	public static function get_default_element_value($element_id, $key) {
		if (empty(self::$elements)) self::get_elements();

		if (!isset(self::$elements_array[$element_id]) || !isset(self::$elements_array[$element_id]["data"][$key])) return false;

		return self::$elements_array[$element_id]["data"][$key];
	}

	/**
		validate options
	**/
	public static function validate_option($setting = array(), $value = "", $id = 0) {
		// invalid function call
		if (!is_array($setting)) wp_die(__("Function validate_option was called incorrectly.", "ezfc"));
		// do not mess with arrays
		if (is_array($value)) return $value;

		// set to input by default
		$setting["type"] = empty($setting["type"]) ? "input" : $setting["type"];

		switch ($setting["type"]) {
			case "yesno":
				$value = empty($value) ? 0 : 1;
			break;

			case "email":
				// normalize
				$emails = array($value);

				// multiple
				if (strpos($value, ",") !== false) {
					$emails = explode(",", $value);
				}

				foreach ($emails as $email) {
					$email = trim($email);
					
					if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
						return self::return_option_error(__("Please enter a valid email address.", "ezfc"), $id);
					}
				}
			break;

			case "email_sender_name":
				$invalid = false;
				$sendername = trim($value);

				// disable check for dynamic values
				if (!empty($sendername) && strpos($sendername, "{{") === false) {
					$email_split_open = explode("<", $value);

					if (count($email_split_open) < 2) {
						// check for email only
						if (!filter_var($email_split_open[0], FILTER_VALIDATE_EMAIL)) {
							$invalid = true;
						}
					}
					else {
						$email_split_close = explode(">", $email_split_open[1]);

						if (count($email_split_close) < 2) {
							$invalid = true;
						}
						else {
							$email_check = $email_split_close[0];

							if (empty($email_check) || !filter_var($email_check, FILTER_VALIDATE_EMAIL)) {
								$invalid = true;
							}
						}
					}
				}

				if ($invalid) {
					return self::return_option_error(sprintf(__("Invalid syntax. Please use the following syntax: %s", "ezfc"), "Sendername &lt;sender@mail.com&gt;"), $id);
				}
			break;

			default:
				// no action
				$value = stripslashes($value);
			break;
		}

		return $value;
	}

	public static function return_option_error($msg, $id = 0) {
		return Ezfc_Functions::send_message("error", $msg, $id);
	}
}