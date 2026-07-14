<?php

defined( 'ABSPATH' ) OR exit;

use Dompdf\Dompdf;
use Dompdf\Options;
define("EZFC_EXT_PDF_VERSION", "1.0.0");

class EZFC_Extension_PDF {
	private $attachment_file;
	private $frontend;
	private $realfile;

	public $dompdf;
	public $options;
	public $submission_data;

	/**
		constructor
	**/
	function __construct($frontend = null) {
		if ($frontend) $this->frontend = $frontend;

		// frontend submission action
		add_action("ezfc_after_submission_before_send_mails", array($this, "frontend_submission"), 10, 7);

		// add attachment filter
		add_filter("ezfc_submission_attachments_admin", array($this, "add_attachment_admin"), 10, 3);
		add_filter("ezfc_submission_attachments_customer", array($this, "add_attachment_customer"), 10, 3);

		// after submission
		add_action("ezfc_after_submission", array($this, "after_submission"), 10, 1);
		
		$this->dirname = get_option("ezfc_ext_pdf_dirname");

		$this->setup();
	}

	/**
		set up dompdf
	**/
	public function setup() {
		if (!defined("DOMPDF_UNICODE_ENABLED")) {
			define("DOMPDF_UNICODE_ENABLED", true);
		}

		// load dompdf
		require_once(EZFC_PATH . "lib/dompdf/autoload.inc.php");

		// dompdf options
		$this->options = new Options();

		$this->options->set("isHtml5ParserEnabled", true);
		$this->options->set("logOutputFile", false);
		$this->options->set("tempDir", $this->dirname);

		// allow remote files
		if (get_option("ezfc_pdf_allow_remote", 1) == 1) {
			$this->options->setIsRemoteEnabled(true);
		}

		$this->dompdf = new Dompdf();
	}

	/**
		frontend submission
	**/
	public function frontend_submission($insert_id, $total, $user_mail, $id, $output_data, $submission_data, $replace_values_text) {
		if ($submission_data["options"]["pdf_enable"] == 0) return;

		global $wp_filesystem;

		// check if pdf dir exists
		if ( ! file_exists( $this->dirname ) ) {
			$this->frontend->debug(__("PDF dir does not exist: {$this->dirname}", "ezfc"));
			return;
		}

		$this->submission_data = $submission_data;

		// additional options
		do_action("ezfc_pdf_options", $this->options);

		// page setup
		$pdf_page_setup = array(
			"orientation" => get_option("ezfc_pdf_page_orientation", "portrait"),
			"size" => get_option("ezfc_pdf_page_size", "letter")
		);

		// CSS
		$custom_css  = "* { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; }";
		$custom_css .= get_option("ezfc_pdf_css_styles", "");
		$custom_css  = apply_filters("ezfc_pdf_css_styles", $custom_css, $insert_id);

		$add_header      = apply_filters("ezfc_pdf_add_header", 0, $insert_id, $submission_data);
		$add_footer      = apply_filters("ezfc_pdf_add_footer", 0, $insert_id, $submission_data);
		$add_header_text = "";
		$add_footer_text = "";

		if (get_option("ezfc_license_activated", 0) == 0) {
			$add_footer = true;
			$add_footer_text .= "Powered by <a href='https://www.ezplugins.de/link/ezfc-pdf-ulc/'>ez Form Calculator</a>";
		}

		if ($add_header) {
			$add_header_css  = "header { position: fixed; top: -20px; left: 0px; right: 0px; height: 40px; }";
			$add_header_css  = apply_filters("ezfc_pdf_add_header_css", $add_header_css);

			$add_header_text = apply_filters("ezfc_pdf_add_header_text", $add_header_text, $insert_id, $submission_data, $this->dompdf);
			$add_header_text = "<header>{$add_header_text}</header>";

			$custom_css .= $add_header_css;
		}
		if ($add_footer) {
			$add_footer_css = "footer { position: fixed; bottom: 0px; left: 0px; right: 0px; height: 40px; }";
			$add_footer_css = apply_filters("ezfc_pdf_add_footer_css", $add_footer_css);

			$add_footer_text = apply_filters("ezfc_pdf_add_footer_text", $add_footer_text, $insert_id, $submission_data, $this->dompdf);
			$add_footer_text = "<footer>{$add_footer_text}</footer>";



			$custom_css .= $add_footer_css;
		}

		// headers
		$pdf_headers = array(
			"<html>",
			"<head>",
			'<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>',
			"<style>",
			$custom_css,
			"</style>",
			"</head>"
		);
		$pdf_headers = apply_filters("ezfc_pdf_header", $pdf_headers);

		// process shortcodes in content
		if (get_option("ezfc_pdf_allow_shortcodes", 1)) {
			$output_data["pdf"] = do_shortcode($output_data["pdf"]);
		}

		// prepare data
		$pdf_content = apply_filters("the_content", $output_data["pdf"]);

		// check for pdf theme
		$theme = $this->submission_data["options"]["pdf_theme"];
		// default theme -> prepare content
		if ($theme == "default") {
			$output_data["pdf"]  = implode("", $pdf_headers);
			$output_data["pdf"] .= "<body>";
			// header
			if ($add_header) $output_data["pdf"] .= $add_header_text;
			// content
			$output_data["pdf"] .= $pdf_content;
			// footer
			if ($add_footer) $output_data["pdf"] .= $add_footer_text;
			$output_data["pdf"] .= "</body>";

			$output_data["pdf"]  = apply_filters("ezfc_pdf_output", $output_data["pdf"], $insert_id);
			$output_data["pdf"] .= "</html>";
		}
		// get content from theme
		else {
			$output_data["pdf"] = $this->format_pdf_content($pdf_content);
		}

		$this->dompdf->setOptions($this->options);
		// page setup
		$this->dompdf->set_paper($pdf_page_setup["size"], $pdf_page_setup["orientation"]);
		// html output
		$this->dompdf->load_html($output_data["pdf"]);
		$this->dompdf->render();

		$output = @$this->dompdf->output();

		// create pdf dir
		//$seed = md5(get_option("ezfc_ext_pdf_seed", random(10, 99999)));
		$seed = md5(microtime(true));
		$filename = "submission-{$insert_id}-{$seed}.pdf";
		$this->realfile = $this->dirname . $filename;

		// build pdf link
		$upload_dir = wp_upload_dir();
		$this->frontend->replace_values["pdf_url"] = trailingslashit($upload_dir["baseurl"]) . Ezfc_Functions::$folders["pdf"] . "/" . $filename;
		$this->frontend->replace_values["pdf_filename"] = $filename;

		// replace filename placeholders
		$attachment_filename = empty($submission_data["options"]["pdf_filename"]) ? "form" : $submission_data["options"]["pdf_filename"];
		$attachment_filename = $this->frontend->replace_values_text($attachment_filename, $replace_values_text);

		$this->attachment_file = $this->dirname . $attachment_filename . ".pdf";

    	$bytes_written = @file_put_contents($this->realfile, $output);
    	if ($bytes_written === false) {
    		$this->frontend->debug(__("Unable to write PDF file.", "ezfc"));
    	}
    	else {
    		// copy file to have a different filename without the generated seed
    		@copy($this->realfile, $this->attachment_file);
    		update_option("ezfc_submission_pdf_file_{$insert_id}", $this->realfile);
    	}
	}

	public function format_pdf_content($content = "") {
		global $style;
		global $texts;

		// default styles
		$style = array(
			"css_styles" => addslashes(get_option("ezfc_pdf_css_styles", "")),
			"logo"       => get_option("ezfc_logo", ""),
		);

		$texts = array(
			"company"              => get_option("ezfc_company_name", ""),
			"text_company_details" => get_option("ezfc_pdf_text_company", ""),
			"text_footer"          => get_option("ezfc_pdf_text_footer", ""),
			"text_header"          => get_option("ezfc_pdf_text_header", "")
		);

		// email theme
		$theme = sanitize_file_name($this->submission_data["options"]["pdf_theme"]);
		// get template content
		$template = $this->frontend->get_template("pdf/{$theme}", "pdf");

		// template found
		if ($template) {
			// replace custom text with content
			$content = str_replace("{{custom_text}}", $content, $template);
			$content = str_replace("{{logo}}", $style["logo"], $content);

			foreach ($texts as $text_key => $text_val) {
				$text_replace = do_shortcode(nl2br($text_val));
				$content = str_replace("{{" . $text_key . "}}", $text_replace, $content);
			}
		}

		return $content;
	}

	/**
		add attachment (admin)
	**/
	public function add_attachment_admin($attachments, $submission_id, $form_options) {
		if ($form_options["pdf_enable"] == 0 || $form_options["pdf_send_to_admin"] == 0) return $attachments;

		if (empty($this->attachment_file)) {
			$tmp_filename = get_option("ezfc_submission_pdf_file_{$submission_id}");

			if (!file_exists($tmp_filename)) return $attachments;

			// replace filename placeholders
			$attachment_filename = empty($form_options["pdf_filename"]) ? "form" : $form_options["pdf_filename"];
			$attachment_filename = $this->frontend->replace_values_text($attachment_filename);

			$this->attachment_file = $this->dirname . $attachment_filename . ".pdf";

			@copy($tmp_filename, $this->attachment_file);

			if (file_exists($this->attachment_file)) {
				$attachments[] = $this->attachment_file;
			}
		}
		else {
			$attachments[] = $this->attachment_file;
		}

		return $attachments;
	}

	/**
		add attachment (user)
	**/
	public function add_attachment_customer($attachments, $submission_id, $form_options) {
		if ($form_options["pdf_enable"] == 0 || $form_options["pdf_send_to_customer"] == 0) return $attachments;

		if (empty($this->attachment_file)) {
			$tmp_filename = get_option("ezfc_submission_pdf_file_{$submission_id}");

			if (!file_exists($tmp_filename)) return $attachments;

			// replace filename placeholders
			$attachment_filename = empty($form_options["pdf_filename"]) ? "form" : $form_options["pdf_filename"];
			$attachment_filename = $this->frontend->replace_values_text($attachment_filename);

			$this->attachment_file = $this->dirname . $attachment_filename . ".pdf";

			@copy($tmp_filename, $this->attachment_file);

			if (file_exists($this->attachment_file)) {
				$attachments[] = $this->attachment_file;
			}
		}
		else {
			$attachments[] = $this->attachment_file;
		}

		return $attachments;
	}

	/**
		add attachment
	**/
	public function after_submission($submission_id) {
		// remove temporary file
		@unlink($this->attachment_file);

		if (get_option("ezfc_pdf_save_file", 0) == 0) {
			// remove generated file
			$seed     = get_option("ezfc_ext_pdf_seed");
			$filename = "submission-{$submission_id}-{$seed}.pdf";
			$realfile = $this->dirname . $filename;

			@unlink($realfile);
		}
	}

	/**
		test
	**/
	public function test() {
		// build test content
		$content  = "<html><head><style>body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; }</style></head><body>";
		$content .= "<h2>" . __("Test PDF", "ezfc") . "</h2>";
		$content .= "<p>" . __("Test content generated by ez Form Calculator", "ezfc") . "</p>";
		$content .= "<h3>" . __("Test Image", "ezfc") . "</h3>";
		$content .= "<img src='http://www.ezplugins.de/wp-content/uploads/banner-350x250.jpg' alt='" . __("The test image should be displayed here.", "ezfc") . "' />";
		$content .= "<h3>" . __("Test Characters", "ezfc") . "</h3>";
		$content .= "<h4>" . __("Basic Latin", "ezfc") . "</h4>";
		$content .= "<p>! \" # $ % & ' ( ) * + , - . / 0 1 2 3 4 5 6 7 8 9 : ; &lt; = %gt; ? @ A B C D E F G H I J K L M N O P Q R S T U V W X Y Z [ \ ] ^ _ ` a b c d e f g h i j k l m n o p q r s t u v w x y z { | } ~</p>";
		$content .= "<h4>" . __("Latin-1 Supplement", "ezfc") . "</h4>";
		$content .= "<p>¡ ¢ £ ¤ ¥ ¦ § ¨ © ª « ¬ ­ ® ¯ ° ± ² ³ ´ µ ¶ · ¸ ¹ º » ¼ ½ ¾ ¿ À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö × Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ÷ ø ù ú û ü ý þ ÿ</p>";
		$content .= "<h4>" . __("Test Coffee", "ezfc") . "</h4>";
		$content .= "</p>c[_]</p>";
		$content .= "</body></html>";

		$this->dompdf->setOptions($this->options);
		// page setup
		$this->dompdf->set_paper("A4", "portrait");
		// html output
		$this->dompdf->load_html($content);
		$this->dompdf->render();

		$output = $this->dompdf->output();

		// create pdf dir
		$filename = "test.pdf";
		$this->realfile = $this->dirname . $filename;

    	$bytes_written = file_put_contents($this->realfile, $output);

    	return $bytes_written === false ? false : $this->realfile;
	}
}