<article <?php post_class( 'service-listing__item' ); ?>>
	<a class="service-listing__link" href="<?php the_permalink(); ?>">
		<span class="service-listing__number" aria-hidden="true"></span>
		<span class="service-listing__title"><?php the_title(); ?></span>
		<span class="service-listing__summary">
			<?php echo sharkdevelop_project_meta( 'sd_service_summary' ) ?: esc_html( get_the_excerpt() ); ?>
		</span>
		<span class="service-listing__action button button--light">
			<span class="button__label"><?php esc_html_e( 'Learn more', 'sharkdevelop' ); ?></span>
			<svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M5 12h14" />
				<path d="m13 6 6 6-6 6" />
			</svg>
		</span>
	</a>
</article>
