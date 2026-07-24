<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'sharkdevelop_enqueue_assets' );

function sharkdevelop_enqueue_assets(): void {
	$theme_path = get_template_directory();
	$theme_version = wp_get_theme()->get( 'Version' );
	$style_version = filemtime( $theme_path . '/assets/css/app.css' ) ?: $theme_version;
	$lenis_version = filemtime( $theme_path . '/assets/js/lenis.min.js' ) ?: $theme_version;
	$app_version = filemtime( $theme_path . '/assets/js/app.js' ) ?: $theme_version;

	wp_enqueue_style(
		'sharkdevelop-app',
		get_template_directory_uri() . '/assets/css/app.css',
		array(),
		$style_version
	);

	wp_enqueue_script(
		'sharkdevelop-lenis',
		get_template_directory_uri() . '/assets/js/lenis.min.js',
		array(),
		$lenis_version,
		true
	);

	wp_enqueue_script(
		'sharkdevelop-app',
		get_template_directory_uri() . '/assets/js/app.js',
		array( 'sharkdevelop-lenis' ),
		$app_version,
		true
	);
}
