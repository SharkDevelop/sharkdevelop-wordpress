<article <?php post_class( 'entry' ); ?>>
	<header class="entry__header">
		<h1><?php the_title(); ?></h1>
	</header>

	<div class="entry__content">
		<?php the_content(); ?>
	</div>
</article>

