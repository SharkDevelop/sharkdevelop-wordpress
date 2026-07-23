<article <?php post_class( 'content-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="content-card__media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="content-card__body">
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php the_excerpt(); ?>
	</div>
</article>

