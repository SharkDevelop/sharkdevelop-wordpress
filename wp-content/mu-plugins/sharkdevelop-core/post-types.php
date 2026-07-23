<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_post_types' );

function sharkdevelop_register_post_types(): void {
	register_post_type(
		'sd_project',
		array(
			'labels'       => array(
				'name'          => 'Projects',
				'singular_name' => 'Project',
				'add_new_item'  => 'Add Project',
				'edit_item'     => 'Edit Project',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-portfolio',
			'rewrite'      => array(
				'slug'       => 'case-studies',
				'with_front' => false,
			),
			'show_in_rest' => true,
			'supports'     => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'custom-fields',
			),
		)
	);

	register_post_type(
		'sd_service',
		array(
			'labels'       => array(
				'name'          => 'Services',
				'singular_name' => 'Service',
				'add_new_item'  => 'Add Service',
				'edit_item'     => 'Edit Service',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-admin-tools',
			'rewrite'      => array(
				'slug'       => 'services',
				'with_front' => false,
			),
			'show_in_rest' => true,
			'supports'     => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'page-attributes',
				'custom-fields',
			),
		)
	);
}
