<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_project_meta' );
add_action( 'init', 'sharkdevelop_register_service_meta' );

function sharkdevelop_register_project_meta(): void {
	$fields = array(
		'sd_project_summary' => array(
			'type'        => 'string',
			'description' => 'Short project summary.',
		),
		'sd_project_client'  => array(
			'type'        => 'string',
			'description' => 'Client or product name.',
		),
		'sd_project_year'    => array(
			'type'        => 'string',
			'description' => 'Project year or period.',
		),
		'sd_project_tools'   => array(
			'type'        => 'string',
			'description' => 'Comma-separated tools and technologies.',
		),
		'sd_project_url'     => array(
			'type'        => 'string',
			'description' => 'Project URL.',
		),
		'sd_project_gallery' => array(
			'type'        => 'array',
			'description' => 'Gallery attachment IDs.',
			'items'       => array(
				'type' => 'integer',
			),
		),
		'sd_project_featured' => array(
			'type'        => 'boolean',
			'description' => 'Show project in featured sections.',
		),
		'sd_project_home_order' => array(
			'type'        => 'integer',
			'description' => 'Homepage project ordering.',
		),
		'sd_project_theme' => array(
			'type'        => 'string',
			'description' => 'Visual theme token for project pages and cards.',
		),
		'sd_project_card_size' => array(
			'type'        => 'string',
			'description' => 'Project card size token.',
		),
	);

	foreach ( $fields as $key => $args ) {
		sharkdevelop_register_meta_field( 'sd_project', $key, $args );
	}
}

function sharkdevelop_register_service_meta(): void {
	$fields = array(
		'sd_service_summary' => array(
			'type'        => 'string',
			'description' => 'Short service summary.',
		),
		'sd_service_icon' => array(
			'type'        => 'integer',
			'description' => 'Service icon attachment ID.',
		),
		'sd_service_featured' => array(
			'type'        => 'boolean',
			'description' => 'Show service in featured sections.',
		),
		'sd_service_home_order' => array(
			'type'        => 'integer',
			'description' => 'Homepage service ordering.',
		),
	);

	foreach ( $fields as $key => $args ) {
		sharkdevelop_register_meta_field( 'sd_service', $key, $args );
	}
}

function sharkdevelop_register_meta_field( string $post_type, string $key, array $args ): void {
	$schema = array(
		'type'              => $args['type'],
		'description'       => $args['description'],
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sharkdevelop_sanitize_meta_field',
		'auth_callback'     => static function (): bool {
			return current_user_can( 'edit_posts' );
		},
	);

	if ( isset( $args['items'] ) ) {
		$schema['show_in_rest'] = array(
			'schema' => array(
				'type'  => 'array',
				'items' => $args['items'],
			),
		);
	}

		register_post_meta(
			$post_type,
			$key,
			$schema
		);
}

function sharkdevelop_sanitize_meta_field( mixed $value, string $key ): mixed {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_array( $value ) ) {
		return array_values( array_filter( array_map( 'absint', $value ) ) );
	}

	if ( str_ends_with( $key, '_url' ) ) {
		return esc_url_raw( (string) $value );
	}

	return sanitize_text_field( (string) $value );
}
