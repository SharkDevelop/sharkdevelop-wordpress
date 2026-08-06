<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sharkdevelop_services_page_default_content(): string {
	return <<<'HTML'
<!-- wp:group {"className":"services-hero__content","layout":{"type":"default"}} -->
<div class="wp-block-group services-hero__content"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">What we do</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"anchor":"services-title"} -->
<h1 class="wp-block-heading" id="services-title">Digital product development for ambitious teams</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We design, build, and evolve digital products that solve real business problems.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:sharkdevelop/services-list {"eyebrow":"Our capabilities","title":"End-to-end product expertise"} /-->

<!-- wp:sharkdevelop/services-benefits /-->
HTML;
}

function sharkdevelop_services_page_block( int $page_id, string $class_name ): array {
	$blocks = parse_blocks( (string) get_post_field( 'post_content', $page_id ) );

	foreach ( $blocks as $block ) {
		$classes = $block['attrs']['className'] ?? '';

		if ( str_contains( $classes, $class_name ) ) {
			return $block;
		}
	}

	return array();
}

function sharkdevelop_render_services_page_block( int $page_id, string $class_name ): void {
	$block = sharkdevelop_services_page_block( $page_id, $class_name );

	if ( $block ) {
		echo render_block( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

function sharkdevelop_services_page_named_block( int $page_id, string $block_name ): array {
	$blocks = parse_blocks( (string) get_post_field( 'post_content', $page_id ) );

	foreach ( $blocks as $block ) {
		if ( $block_name === $block['blockName'] ) {
			return $block;
		}
	}

	return array();
}

function sharkdevelop_render_services_page_named_block( int $page_id, string $block_name ): void {
	$block = sharkdevelop_services_page_named_block( $page_id, $block_name );

	if ( $block ) {
		echo render_block( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
