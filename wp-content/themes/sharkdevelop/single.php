<?php
get_header();
?>

<main class="site-main">
	<div class="container stack">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/page' );
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();

