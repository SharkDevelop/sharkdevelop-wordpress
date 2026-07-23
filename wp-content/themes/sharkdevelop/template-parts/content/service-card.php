<article <?php post_class( 'service-card' ); ?>>
	<a class="service-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<span class="service-card__media">
				<?php the_post_thumbnail( 'medium_large' ); ?>
			</span>
		<?php endif; ?>

		<span class="service-card__body">
			<span class="service-card__title"><?php the_title(); ?></span>
			<span class="service-card__summary">
				<?php echo sharkdevelop_project_meta( 'sd_service_summary' ) ?: esc_html( get_the_excerpt() ); ?>
			</span>
		</span>
	</a>
</article>

