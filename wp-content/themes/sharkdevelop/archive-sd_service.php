<?php
get_header();
?>

<main class="site-main site-main--services">
	<section class="services-hero" aria-labelledby="services-title">
		<div class="container services-hero__inner">
			<div class="services-hero__content">
				<p class="eyebrow"><?php esc_html_e( 'What we do', 'sharkdevelop' ); ?></p>
				<h1 id="services-title"><?php esc_html_e( 'Digital product development for ambitious teams', 'sharkdevelop' ); ?></h1>
				<p><?php esc_html_e( 'We design, build, and evolve digital products that solve real business problems.', 'sharkdevelop' ); ?></p>
			</div>
			<div class="services-hero__art" aria-hidden="true">
				<span></span>
			</div>
		</div>
	</section>

	<section class="services-listing" aria-labelledby="services-list-title">
		<div class="container">
			<div class="services-listing__intro">
				<p class="eyebrow"><?php esc_html_e( 'Our capabilities', 'sharkdevelop' ); ?></p>
				<h2 id="services-list-title"><?php esc_html_e( 'End-to-end product expertise', 'sharkdevelop' ); ?></h2>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="services-listing__items">
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'template-parts/content/service-list-item' ); ?>
					<?php endwhile; ?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php endif; ?>
		</div>
	</section>

	<section class="services-benefits" aria-labelledby="services-benefits-title">
		<div class="container">
			<p class="eyebrow services-benefits__eyebrow"><?php esc_html_e( 'Why Shark Develop', 'sharkdevelop' ); ?></p>

			<div class="services-benefits__inner">
			<div class="services-benefits__intro">
				<h2 id="services-benefits-title"><?php esc_html_e( 'A better way to build a product', 'sharkdevelop' ); ?></h2>
			</div>

			<div class="services-benefits__items">
				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--target" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Product thinking from day one', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'We turn business goals and constraints into clear product decisions before development begins.', 'sharkdevelop' ); ?></p>
				</article>

				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--route" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Clarity at every stage', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'You see priorities, progress, and next steps throughout the work, with decisions kept visible.', 'sharkdevelop' ); ?></p>
				</article>

				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--layers" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Ready for what comes next', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'We build maintainable foundations that support new features, integrations, and business growth.', 'sharkdevelop' ); ?></p>
				</article>

				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--globe" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Working across time zones', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'Clear routines and asynchronous communication keep the project moving, wherever your team is based.', 'sharkdevelop' ); ?></p>
				</article>

				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--spark" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Impossible is a starting point', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'We take on complex product and technical challenges, working through constraints to find a practical path to the intended result.', 'sharkdevelop' ); ?></p>
				</article>

				<article class="services-benefits__item">
					<span class="services-benefits__icon services-benefits__icon--sliders" aria-hidden="true"></span>
					<h3><?php esc_html_e( 'Flexible work terms', 'sharkdevelop' ); ?></h3>
					<p><?php esc_html_e( 'We work on a Fixed Scope basis when requirements are clear and use Time & Materials for discovery, evolving products, and ongoing delivery.', 'sharkdevelop' ); ?></p>
				</article>
			</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
