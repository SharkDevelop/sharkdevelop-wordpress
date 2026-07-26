<?php
/**
 * Plugin Name: Shark Develop Core
 * Description: Project-specific content types, taxonomies, and metadata.
 * Author: Shark Develop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sharkdevelop_core_dir = __DIR__ . '/sharkdevelop-core';

require_once $sharkdevelop_core_dir . '/post-types.php';
require_once $sharkdevelop_core_dir . '/taxonomies.php';
require_once $sharkdevelop_core_dir . '/meta.php';
require_once $sharkdevelop_core_dir . '/migration.php';
