<?php
get_header();
?>

<main class="site-main site-main--editable">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php
get_footer();
