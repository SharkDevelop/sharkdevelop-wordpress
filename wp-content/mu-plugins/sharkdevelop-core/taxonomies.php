<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_taxonomies' );

function sharkdevelop_register_taxonomies(): void {
	register_taxonomy(
		'sd_project_type',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Project Types',
				'singular_name' => 'Project Type',
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'project-type',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'sd_project_platform',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Platforms',
				'singular_name' => 'Platform',
			),
			'public'       => true,
			'hierarchical' => false,
			'sort'         => true,
			'args'         => array(
				'orderby' => 'term_order',
				'order'   => 'ASC',
			),
			'rewrite'      => array(
				'slug'       => 'platform',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'sd_project_technology',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Technologies',
				'singular_name' => 'Technology',
			),
			'public'       => true,
			'hierarchical' => false,
			'sort'         => true,
			'args'         => array(
				'orderby' => 'term_order',
				'order'   => 'ASC',
			),
			'meta_box_cb'  => false,
			'rewrite'      => array(
				'slug'       => 'technology',
				'with_front' => false,
			),
			'show_in_rest' => false,
		)
	);

	register_taxonomy(
		'sd_project_tag',
		array( 'sd_project' ),
		array(
			'labels'       => array(
				'name'          => 'Tags',
				'singular_name' => 'Tag',
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => false,
			'hierarchical' => false,
			'sort'         => true,
			'args'         => array(
				'orderby' => 'term_order',
				'order'   => 'ASC',
			),
			'meta_box_cb'  => false,
		)
	);

	register_taxonomy(
		'sd_project_service',
		array( 'sd_project', 'sd_service' ),
		array(
			'labels'       => array(
				'name'          => 'Service Areas',
				'singular_name' => 'Service Area',
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'project-service',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);
}

/**
 * Renders an ordered taxonomy field without WordPress's alphabetical tag-chip sorting.
 * The comma-separated order is stored by the taxonomy's native `sort` setting.
 *
 * @param WP_Post               $post Current project.
 * @param string                $taxonomy Taxonomy name.
 */
function sharkdevelop_render_ordered_project_terms_field( WP_Post $post, string $taxonomy ): void {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$taxonomy_object = get_taxonomy( $taxonomy );
	$terms = wp_get_object_terms(
		$post->ID,
		$taxonomy,
		array(
			'fields'  => 'names',
			'orderby' => 'term_order',
			'order'   => 'ASC',
		)
	);
	$terms = is_wp_error( $terms ) ? array() : $terms;
	$field_id = 'tax-input-' . $taxonomy;
	$description = 'sd_project_tag' === $taxonomy
		? __( 'Separate tags with commas. The saved order is used on project cards.', 'sharkdevelop' )
		: __( 'Separate technologies with commas. The saved order is retained.', 'sharkdevelop' );
	?>
	<p>
		<label for="<?php echo esc_attr( $field_id ); ?>"><strong><?php echo esc_html( $taxonomy_object->labels->name ); ?></strong></label>
		<textarea class="large-text" data-wp-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( 'tax_input[' . $taxonomy . ']' ); ?>" rows="3" <?php disabled( ! current_user_can( $taxonomy_object->cap->assign_terms ) ); ?>><?php echo esc_textarea( implode( ', ', $terms ) ); ?></textarea>
	</p>
	<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php
	$popular_terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'number'     => 10,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	if ( ! is_wp_error( $popular_terms ) && ! empty( $popular_terms ) ) :
		?>
		<p class="description"><?php esc_html_e( 'Frequently used:', 'sharkdevelop' ); ?></p>
		<p class="sharkdevelop-popular-terms">
			<?php foreach ( $popular_terms as $popular_term ) : ?>
				<button class="button-link sharkdevelop-popular-term" data-target="<?php echo esc_attr( $field_id ); ?>" data-term="<?php echo esc_attr( $popular_term->name ); ?>" type="button"><?php echo esc_html( $popular_term->name ); ?></button>
			<?php endforeach; ?>
		</p>
	<?php endif; ?>
	<?php
}
