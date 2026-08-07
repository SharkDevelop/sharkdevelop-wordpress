<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow     = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : __( 'Selected mobile work', 'sharkdevelop' );
$title       = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : __( 'Mobile products built around real use', 'sharkdevelop' );
$project_ids = isset( $attributes['projectIds'] ) && is_array( $attributes['projectIds'] ) ? array_values( array_filter( array_map( 'absint', $attributes['projectIds'] ) ) ) : array();

if ( empty( $project_ids ) ) {
	if ( is_admin() ) {
		?>
		<div class="selected-projects__empty">
			<?php esc_html_e( 'Select projects in the block settings.', 'sharkdevelop' ); ?>
		</div>
		<?php
	}

	return;
}

$projects = new WP_Query(
	array(
		'post_type'      => 'sd_project',
		'post_status'    => 'publish',
		'posts_per_page' => count( $project_ids ),
		'post__in'       => $project_ids,
		'orderby'        => 'post__in',
	)
);

if ( ! $projects->have_posts() ) {
	return;
}
?>
<section class="service-section service-section--selected-projects alignfull">
	<div class="container">
		<div class="service-section__heading">
			<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>

		<?php echo sharkdevelop_render_project_showcase( $projects ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
