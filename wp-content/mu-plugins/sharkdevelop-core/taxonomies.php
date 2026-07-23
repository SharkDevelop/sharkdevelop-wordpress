<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_taxonomies' );

function sharkdevelop_register_taxonomies(): void {
	register_taxonomy(
		'sd_project_type',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Project Types',
				'singular_name' => 'Project Type',
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'project-type',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'sd_project_platform',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Platforms',
				'singular_name' => 'Platform',
			),
			'public'       => true,
			'hierarchical' => false,
			'rewrite'      => array(
				'slug'       => 'platform',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'sd_project_technology',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Technologies',
				'singular_name' => 'Technology',
			),
			'public'       => true,
			'hierarchical' => false,
			'rewrite'      => array(
				'slug'       => 'technology',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'sd_project_service',
		array( 'sd_project', 'sd_service' ),
		array(
			'labels'       => array(
				'name'          => 'Service Areas',
				'singular_name' => 'Service Area',
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'project-service',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);
}
