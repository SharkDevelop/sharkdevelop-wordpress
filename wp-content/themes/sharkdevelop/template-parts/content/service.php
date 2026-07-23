<article <?php post_class( 'service' ); ?>>
	<header class="page-hero">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Service', 'sharkdevelop' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<?php if ( sharkdevelop_project_meta( 'sd_service_summary' ) ) : ?>
				<p><?php echo sharkdevelop_project_meta( 'sd_service_summary' ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<div class="container entry__content">
		<?php the_content(); ?>
	</div>
</article>
