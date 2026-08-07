<?php
get_header();
?>

<main class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( sharkdevelop_has_service_landing_content( get_the_ID() ) ) :
			?>
			<article <?php post_class( 'service service--landing' ); ?>>
				<?php the_content(); ?>
			</article>
			<?php
		else :
			get_template_part( 'template-parts/content/service' );
		endif;
	endwhile;
	?>
</main>

<?php
get_footer();
