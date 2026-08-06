<?php
/**
 * Template Name: Services
 * Template Post Type: page
 *
 * @package SharkDevelop
 */

get_header();

while ( have_posts() ) :
	the_post();
	$page_id = get_the_ID();
	?>
	<main class="site-main site-main--services">
		<section class="services-hero" aria-labelledby="services-title">
			<div class="container services-hero__inner">
				<?php sharkdevelop_render_services_page_block( $page_id, 'services-hero__content' ); ?>
				<div class="services-hero__art" aria-hidden="true"><span></span></div>
			</div>
		</section>

		<section class="services-listing" aria-labelledby="services-list-title">
			<div class="container">
				<?php sharkdevelop_render_services_page_named_block( $page_id, 'sharkdevelop/services-list' ); ?>
			</div>
		</section>

		<section class="services-benefits" aria-labelledby="services-benefits-title">
			<div class="container">
				<?php sharkdevelop_render_services_page_named_block( $page_id, 'sharkdevelop/services-benefits' ); ?>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
