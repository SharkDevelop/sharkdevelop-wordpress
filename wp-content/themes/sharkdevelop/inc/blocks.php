<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_blocks' );

function sharkdevelop_register_blocks(): void {
	register_block_type( get_theme_file_path( 'blocks/services-list' ) );
	register_block_type( get_theme_file_path( 'blocks/services-benefits' ) );
}
