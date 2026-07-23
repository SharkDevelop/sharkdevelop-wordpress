<?php
get_header();
?>

<main class="site-main">
	<section class="page-hero">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'What we do', 'sharkdevelop' ); ?></p>
			<h1><?php esc_html_e( 'Services', 'sharkdevelop' ); ?></h1>
		</div>
	</section>

	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php get_template_part( 'template-parts/content/service-card' ); ?>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();

