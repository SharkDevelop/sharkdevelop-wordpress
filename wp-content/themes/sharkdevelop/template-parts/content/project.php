<article <?php post_class( 'project' ); ?>>
	<header class="project-hero">
		<div class="container project-hero__inner">
			<div class="project-hero__content">
				<p class="eyebrow"><?php echo sharkdevelop_post_terms( get_the_ID(), 'sd_project_platform' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php echo sharkdevelop_project_meta( 'sd_project_summary' ) ?: esc_html( get_the_excerpt() ); ?></p>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="project-hero__media">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<div class="container project__body">
		<aside class="project-facts">
			<?php if ( sharkdevelop_project_meta( 'sd_project_client' ) ) : ?>
				<p><strong><?php esc_html_e( 'Client', 'sharkdevelop' ); ?></strong><br><?php echo sharkdevelop_project_meta( 'sd_project_client' ); ?></p>
			<?php endif; ?>
			<?php if ( sharkdevelop_project_meta( 'sd_project_year' ) ) : ?>
				<p><strong><?php esc_html_e( 'Year', 'sharkdevelop' ); ?></strong><br><?php echo sharkdevelop_project_meta( 'sd_project_year' ); ?></p>
			<?php endif; ?>
			<?php if ( sharkdevelop_project_meta( 'sd_project_tools' ) ) : ?>
				<p><strong><?php esc_html_e( 'Tools', 'sharkdevelop' ); ?></strong><br><?php echo sharkdevelop_project_meta( 'sd_project_tools' ); ?></p>
			<?php endif; ?>
		</aside>

		<div class="entry__content">
			<?php the_content(); ?>
		</div>
	</div>
</article>

