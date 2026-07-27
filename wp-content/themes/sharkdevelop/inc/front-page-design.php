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

	if ( ! is_string( $class_name ) ) {
		return $block_content;
	}

	if ( str_contains( $class_name, 'hero__inner' ) ) {
		return sharkdevelop_insert_before_last_closing_tag(
			$block_content,
			'<div class="wp-block-group hero-art" aria-hidden="true"></div>'
		);
	}

	if ( str_contains( $class_name, 'cta-panel' ) ) {
		return sharkdevelop_insert_before_last_closing_tag(
			$block_content,
			'<div class="cta-crystal cta-crystal--five" aria-hidden="true"><img src="' . esc_url( wp_make_link_relative( get_theme_file_uri( 'assets/images/cta-crystal-5.webp' ) ) ) . '" alt="" decoding="sync" /></div>'
		);
	}

	return $block_content;
}

function sharkdevelop_insert_before_last_closing_tag( string $html, string $insert ): string {
	$position = strrpos( $html, '</div>' );

	if ( false === $position ) {
		return $html;
	}

	return substr_replace( $html, $insert, $position, 0 );
}
