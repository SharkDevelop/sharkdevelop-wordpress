<?php
get_header();
?>

<main class="site-main">
	<div class="container stack">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php get_template_part( 'template-parts/content/post-card' ); ?>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No content found.', 'sharkdevelop' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();

