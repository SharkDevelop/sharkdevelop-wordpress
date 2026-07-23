<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'sharkdevelop_enqueue_assets' );

function sharkdevelop_enqueue_assets(): void {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'sharkdevelop-app',
		get_template_directory_uri() . '/assets/css/app.css',
		array(),
		$theme_version
	);

	wp_enqueue_script(
		'sharkdevelop-app',
		get_template_directory_uri() . '/assets/js/app.js',
		array(),
		$theme_version,
		true
	);
}

