<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_items = array(
	array(
		'icon'        => 'target',
		'title'       => __( 'Product thinking from day one', 'sharkdevelop' ),
		'description' => __( 'We turn business goals and constraints into clear product decisions before development begins.', 'sharkdevelop' ),
	),
	array(
		'icon'        => 'route',
		'title'       => __( 'Clarity at every stage', 'sharkdevelop' ),
		'description' => __( 'You see priorities, progress, and next steps throughout the work, with decisions kept visible.', 'sharkdevelop' ),
	),
	array(
		'icon'        => 'layers',
		'title'       => __( 'Ready for what comes next', 'sharkdevelop' ),
		'description' => __( 'We build maintainable foundations that support new features, integrations, and business growth.', 'sharkdevelop' ),
	),
	array(
		'icon'        => 'globe',
		'title'       => __( 'Working across time zones', 'sharkdevelop' ),
		'description' => __( 'Clear routines and asynchronous communication keep the project moving, wherever your team is based.', 'sharkdevelop' ),
	),
	array(
		'icon'        => 'spark',
		'title'       => __( 'Impossible is a starting point', 'sharkdevelop' ),
		'description' => __( 'We take on complex product and technical challenges, working through constraints to find a practical path to the intended result.', 'sharkdevelop' ),
	),
	array(
		'icon'        => 'sliders',
		'title'       => __( 'Flexible work terms', 'sharkdevelop' ),
		'description' => __( 'We work on a Fixed Scope basis when requirements are clear and use Time & Materials for discovery, evolving products, and ongoing delivery.', 'sharkdevelop' ),
	),
);
$allowed_icons = array( 'target', 'route', 'layers', 'globe', 'spark', 'sliders' );
$eyebrow       = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : __( 'Why Shark Develop', 'sharkdevelop' );
$title         = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : __( 'A better way to build a product', 'sharkdevelop' );
$items         = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : $default_items;
?>
<p class="eyebrow services-benefits__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>

<div class="services-benefits__inner">
	<div class="services-benefits__intro">
		<h2 id="services-benefits-title"><?php echo esc_html( $title ); ?></h2>
	</div>

	<div class="services-benefits__items">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php
			$defaults    = $default_items[ $index ] ?? $default_items[0];
			$icon        = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : $defaults['icon'];
			$item_title  = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : $defaults['title'];
			$description = isset( $item['description'] ) ? sanitize_textarea_field( $item['description'] ) : $defaults['description'];

			if ( ! in_array( $icon, $allowed_icons, true ) ) {
				$icon = $defaults['icon'];
			}
			?>
			<article class="services-benefits__item">
				<span class="services-benefits__icon services-benefits__icon--<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
				<h3><?php echo esc_html( $item_title ); ?></h3>
				<p><?php echo esc_html( $description ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
</div>
