<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'sharkdevelop_theme_setup' );

function sharkdevelop_theme_setup(): void {
	load_theme_textdomain( 'sharkdevelop', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 205,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'sharkdevelop' ),
			'footer'  => __( 'Footer Menu', 'sharkdevelop' ),
		)
	);
}
