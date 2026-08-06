<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'pre_get_posts', 'sharkdevelop_order_services_archive' );

function sharkdevelop_order_services_archive( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'sd_service' ) ) {
		return;
	}

	$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
}
