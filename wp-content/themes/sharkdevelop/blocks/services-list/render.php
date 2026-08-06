<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : __( 'Our capabilities', 'sharkdevelop' );
$title   = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : __( 'End-to-end product expertise', 'sharkdevelop' );
$services = new WP_Query(
	array(
		'post_type'      => 'sd_service',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	)
);
?>
<div class="services-listing__intro">
	<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
	<h2 id="services-list-title"><?php echo esc_html( $title ); ?></h2>
</div>

<?php if ( $services->have_posts() ) : ?>
	<div class="services-listing__items">
		<?php while ( $services->have_posts() ) : ?>
			<?php $services->the_post(); ?>
			<?php get_template_part( 'template-parts/content/service-list-item' ); ?>
		<?php endwhile; ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>
