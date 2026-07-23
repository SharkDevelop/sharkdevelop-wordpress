<?php
get_header();
?>

<main class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content/project' );
	endwhile;
	?>
</main>

<?php
get_footer();

