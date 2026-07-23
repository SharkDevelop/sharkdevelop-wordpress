<article <?php post_class( 'project-card' ); ?>>
	<a class="project-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<span class="project-card__media">
				<?php the_post_thumbnail( 'medium_large' ); ?>
			</span>
		<?php endif; ?>

		<span class="project-card__body">
			<span class="project-card__meta">
				<?php echo sharkdevelop_post_terms( get_the_ID(), 'sd_project_platform' ); ?>
			</span>
			<span class="project-card__title"><?php the_title(); ?></span>
			<span class="project-card__summary">
				<?php echo sharkdevelop_project_meta( 'sd_project_summary' ) ?: esc_html( get_the_excerpt() ); ?>
			</span>
		</span>
	</a>
</article>

