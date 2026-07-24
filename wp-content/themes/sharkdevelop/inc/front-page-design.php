<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/group', 'sharkdevelop_render_front_page_design_art', 10, 2 );

function sharkdevelop_render_front_page_design_art( string $block_content, array $block ): string {
	if ( is_admin() || ! is_front_page() ) {
		return $block_content;
	}

	$class_name = $block['attrs']['className'] ?? '';

	if ( ! is_string( $class_name ) || ! str_contains( $class_name, 'hero__inner' ) ) {
		return $block_content;
	}

	return sharkdevelop_insert_before_last_closing_tag(
		$block_content,
		'<div class="wp-block-group hero-art" aria-hidden="true"></div>'
	);
}

function sharkdevelop_insert_before_last_closing_tag( string $html, string $insert ): string {
	$position = strrpos( $html, '</div>' );

	if ( false === $position ) {
		return $html;
	}

	return substr_replace( $html, $insert, $position, 0 );
}
