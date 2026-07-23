<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sharkdevelop_post_terms( int $post_id, string $taxonomy ): string {
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	return esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) );
}

function sharkdevelop_project_meta( string $key, int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();
	return esc_html( (string) get_post_meta( $post_id, $key, true ) );
}

function sharkdevelop_custom_logo_id(): int {
	$theme_mods = get_option( 'theme_mods_' . get_stylesheet() );

	if ( ! is_array( $theme_mods ) || empty( $theme_mods['custom_logo'] ) ) {
		return 0;
	}

	return absint( $theme_mods['custom_logo'] );
}

function sharkdevelop_attachment_id_by_file( string $file ): int {
	$attachments = get_posts(
		array(
			'fields'         => 'ids',
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $file,
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
		)
	);

	return empty( $attachments ) ? 0 : absint( $attachments[0] );
}

function sharkdevelop_custom_logo( string $class = '', int $logo_id = 0 ): string {
	$logo_id = $logo_id ?: sharkdevelop_custom_logo_id();

	if ( ! $logo_id ) {
		return '';
	}

	$classes = trim( 'custom-logo ' . $class );

	$image = wp_get_attachment_image(
		$logo_id,
		'full',
		false,
		array(
			'class' => $classes,
			'alt'   => get_bloginfo( 'name' ),
		)
	);

	if ( $image ) {
		return $image;
	}

	$logo_url = wp_get_attachment_url( $logo_id );

	if ( ! $logo_url ) {
		return '';
	}

	return sprintf(
		'<img src="%s" class="%s" alt="%s">',
		esc_url( $logo_url ),
		esc_attr( $classes ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

function sharkdevelop_arrow_icon(): string {
	return '<svg class="icon icon--arrow" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
