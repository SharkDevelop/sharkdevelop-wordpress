<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'sharkdevelop migrate-legacy-projects', 'sharkdevelop_migrate_legacy_projects_command' );
}

/**
 * Imports the legacy Cases posts, retains their translation pairs, and removes
 * builder markup from the project body before it becomes sd_project content.
 *
 * @param array<int, string> $args Positional command arguments.
 * @param array<string, mixed> $assoc_args Command options.
 */
function sharkdevelop_migrate_legacy_projects_command( array $args, array $assoc_args ): void {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		WP_CLI::error( 'Polylang is not active.' );
	}

	$dry_run = isset( $assoc_args['dry-run'] );
	$existing_russian_projects = array(
		5046 => 9598, // JBC Watch Tracker.
		5047 => 9600, // Contactless Intercom / NFC Intercom.
		6371 => 9607, // Respect Korea.
		5045 => 9597, // iTopica.
		5041 => 9601, // Zhivika.
		5056 => 9599, // LIMO Air Drive.
		5025 => 9606, // MyBook.
		5043 => 9596, // FStatus.
	);
	$legacy_pairs = array(
		6172 => 5046,
		6219 => 5047,
		6349 => 6371,
		6479 => 5045,
		6576 => 5041,
		6650 => 5028,
		7487 => 8890,
		7553 => 5056,
		7563 => 5025,
		7721 => 5043,
		8931 => 8986,
		9001 => 8907,
	);
	$existing_projects = get_posts(
		array(
			'post_type'      => 'sd_project',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $existing_projects as $project_id ) {
		if ( ! pll_get_post_language( $project_id ) && ! $dry_run ) {
			pll_set_post_language( $project_id, 'ru' );
		}
	}

	foreach ( $legacy_pairs as $english_source_id => $russian_source_id ) {
		$russian_project_id = $existing_russian_projects[ $russian_source_id ] ?? 0;
		$english_project_id = sharkdevelop_import_legacy_project( $english_source_id, 'en', $dry_run );

		if ( ! $russian_project_id ) {
			$russian_project_id = sharkdevelop_import_legacy_project( $russian_source_id, 'ru', $dry_run );
		}

		if ( $dry_run ) {
			WP_CLI::log(
				sprintf(
					'Would link English source %d with Russian source %d.',
					$english_source_id,
					$russian_source_id
				)
			);
			continue;
		}

		pll_set_post_language( $english_project_id, 'en' );
		pll_set_post_language( $russian_project_id, 'ru' );
		pll_save_post_translations(
			array(
				'en' => $english_project_id,
				'ru' => $russian_project_id,
			)
		);

		WP_CLI::success(
			sprintf(
				'Linked project translations: en #%d and ru #%d.',
				$english_project_id,
				$russian_project_id
			)
		);
	}

	if ( $dry_run ) {
		WP_CLI::success( 'Dry run complete. No posts or translations were changed.' );
	}
}

function sharkdevelop_import_legacy_project( int $source_id, string $language, bool $dry_run ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'sd_project',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => 'sd_legacy_source_id',
			'meta_value'     => $source_id,
		)
	);

	if ( ! empty( $existing ) ) {
		return absint( $existing[0] );
	}

	$source = get_post( $source_id );

	if ( ! $source || 'post' !== $source->post_type ) {
		WP_CLI::error( sprintf( 'Legacy source post #%d was not found.', $source_id ) );
	}

	if ( $dry_run ) {
		WP_CLI::log( sprintf( 'Would import %s project: %s.', $language, $source->post_title ) );
		return 0;
	}

	$content = sharkdevelop_clean_legacy_project_content( $source->post_content );
	$project_id = wp_insert_post(
		array(
			'post_type'    => 'sd_project',
			'post_status'  => 'publish',
			'post_title'   => $source->post_title,
			'post_name'    => $source->post_name,
			'post_content' => $content,
			'post_excerpt' => wp_trim_words( wp_strip_all_tags( $content ), 28 ),
		),
		true
	);

	if ( is_wp_error( $project_id ) ) {
		WP_CLI::error( $project_id->get_error_message() );
	}

	update_post_meta( $project_id, 'sd_legacy_source_id', $source_id );
	update_post_meta( $project_id, 'sd_project_summary', wp_trim_words( wp_strip_all_tags( $content ), 28 ) );
	update_post_meta( $project_id, 'sd_project_featured', 0 );
	update_post_meta( $project_id, 'sd_project_home_order', 0 );

	$thumbnail_id = get_post_thumbnail_id( $source_id );

	if ( $thumbnail_id ) {
		set_post_thumbnail( $project_id, $thumbnail_id );
	}

	pll_set_post_language( $project_id, $language );
	wp_update_post(
		array(
			'ID'        => $project_id,
			'post_name' => $source->post_name,
		)
	);

	WP_CLI::log( sprintf( 'Imported %s project #%d: %s.', $language, $project_id, $source->post_title ) );

	return $project_id;
}

function sharkdevelop_clean_legacy_project_content( string $content ): string {
	$content = preg_replace( '#<!--.*?-->#s', '', $content );
	$content = preg_replace( '#</?(?:p|div|section|article|h[1-6]|li|br)[^>]*>#i', "\n\n", $content );
	$content = preg_replace( '#\[(?:/?)(?:et_pb|elementor|vc_|wpb_)[^\]]*\]#i', '', $content );
	$content = strip_shortcodes( $content );
	$content = wp_strip_all_tags( $content, true );
	$content = html_entity_decode( $content, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
	$content = preg_replace( '/[\t ]+/', ' ', $content );
	$content = preg_replace( '/\n\s*\n\s*\n+/', "\n\n", $content );
	$paragraphs = array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', $content ) ) );

	return implode(
		"\n\n",
		array_map(
			static function ( string $paragraph ): string {
				return '<p>' . esc_html( $paragraph ) . '</p>';
			},
			$paragraphs
		)
	);
}
