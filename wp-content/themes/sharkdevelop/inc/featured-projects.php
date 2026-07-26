<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_featured_projects_block' );

function sharkdevelop_register_featured_projects_block(): void {
	register_block_type( get_template_directory() . '/blocks/featured-projects' );
}

function sharkdevelop_render_featured_projects(): string {
	$projects = new WP_Query(
		array(
			'post_type'      => 'sd_project',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => 'sd_project_home_order',
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'title'          => 'ASC',
			),
			'meta_query'     => array(
				array(
					'key'     => 'sd_project_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	if ( ! $projects->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="project-showcase">
		<?php while ( $projects->have_posts() ) : ?>
			<?php
			$projects->the_post();
			$theme = (string) get_post_meta( get_the_ID(), 'sd_project_theme', true );
			$glow = sharkdevelop_normalize_project_glow( (string) get_post_meta( get_the_ID(), 'sd_project_glow', true ) );
			$glow_rgb = sscanf( ltrim( $glow, '#' ), '%02x%02x%02x' );
			$theme_class = array(
				'light'  => 'showcase-card--light',
				'accent' => 'showcase-card--purple',
				'purple' => 'showcase-card--purple',
			);
			$classes = array(
				'showcase-card',
				$theme_class[ $theme ] ?? 'showcase-card--dark',
			);

			$summary = (string) get_post_meta( get_the_ID(), 'sd_project_summary', true );
			$summary = $summary ?: get_the_excerpt();
			$technology_terms = get_the_terms( get_the_ID(), 'sd_project_technology' );
			$platform_terms = get_the_terms( get_the_ID(), 'sd_project_platform' );
			$terms = array();

			if ( ! empty( $technology_terms ) && ! is_wp_error( $technology_terms ) ) {
				$terms = array_merge( $terms, $technology_terms );
			}

			if ( ! empty( $platform_terms ) && ! is_wp_error( $platform_terms ) ) {
				$terms = array_merge( $terms, $platform_terms );
			}
			?>
			<article <?php post_class( 'project-showcase__item' ); ?>>
				<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="--sd-card-glow-rgb: <?php echo esc_attr( implode( ', ', $glow_rgb ) ); ?>;">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="showcase-card__media">
							<?php echo get_the_post_thumbnail( get_the_ID(), 'large', array( 'loading' => 'lazy' ) ); ?>
						</div>
					<?php else : ?>
						<div class="showcase-card__visual" aria-hidden="true"></div>
					<?php endif; ?>
					<a class="showcase-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s project', 'sharkdevelop' ), get_the_title() ) ); ?>"></a>
				</div>
				<div class="showcase-card__details">
					<h3><?php the_title(); ?></h3>
					<div class="showcase-card__more">
						<div>
							<?php if ( $summary ) : ?>
								<p><?php echo esc_html( $summary ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
								<div class="tag-list">
									<?php foreach ( array_slice( $terms, 0, 4 ) as $term ) : ?>
										<span><?php echo esc_html( $term->name ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<a class="button button--light has-arrow-icon" href="<?php the_permalink(); ?>"><span class="button__label"><?php esc_html_e( 'View case study', 'sharkdevelop' ); ?></span></a>
						</div>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();

	return (string) ob_get_clean();
}
