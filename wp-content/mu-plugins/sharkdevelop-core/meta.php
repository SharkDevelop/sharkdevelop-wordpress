<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sharkdevelop_register_project_meta' );
add_action( 'init', 'sharkdevelop_register_service_meta' );
add_action( 'add_meta_boxes_sd_project', 'sharkdevelop_add_project_homepage_meta_box' );
add_action( 'add_meta_boxes_sd_project', 'sharkdevelop_add_project_terms_meta_box' );
add_action( 'admin_enqueue_scripts', 'sharkdevelop_enqueue_project_terms_admin_assets' );
add_action( 'save_post_sd_project', 'sharkdevelop_save_project_homepage_meta' );

function sharkdevelop_register_project_meta(): void {
	$fields = array(
		'sd_project_summary' => array(
			'type'        => 'string',
			'description' => 'Short project summary.',
		),
		'sd_project_client'  => array(
			'type'        => 'string',
			'description' => 'Client or product name.',
		),
		'sd_project_year'    => array(
			'type'        => 'string',
			'description' => 'Project year or period.',
		),
		'sd_project_tools'   => array(
			'type'        => 'string',
			'description' => 'Comma-separated tools and technologies.',
		),
		'sd_project_url'     => array(
			'type'        => 'string',
			'description' => 'Project URL.',
		),
		'sd_project_gallery' => array(
			'type'        => 'array',
			'description' => 'Gallery attachment IDs.',
			'items'       => array(
				'type' => 'integer',
			),
		),
		'sd_project_featured' => array(
			'type'        => 'boolean',
			'description' => 'Show project in featured sections.',
		),
		'sd_project_home_order' => array(
			'type'        => 'integer',
			'description' => 'Homepage project ordering.',
		),
		'sd_project_theme' => array(
			'type'        => 'string',
			'description' => 'Visual theme token for project pages and cards.',
		),
		'sd_project_glow' => array(
			'type'        => 'string',
			'description' => 'Homepage card glow token.',
		),
	);

	foreach ( $fields as $key => $args ) {
		sharkdevelop_register_meta_field( 'sd_project', $key, $args );
	}
}

function sharkdevelop_register_service_meta(): void {
	$fields = array(
		'sd_service_summary' => array(
			'type'        => 'string',
			'description' => 'Short service summary.',
		),
		'sd_service_icon' => array(
			'type'        => 'integer',
			'description' => 'Service icon attachment ID.',
		),
		'sd_service_featured' => array(
			'type'        => 'boolean',
			'description' => 'Show service in featured sections.',
		),
		'sd_service_home_order' => array(
			'type'        => 'integer',
			'description' => 'Homepage service ordering.',
		),
	);

	foreach ( $fields as $key => $args ) {
		sharkdevelop_register_meta_field( 'sd_service', $key, $args );
	}
}

function sharkdevelop_register_meta_field( string $post_type, string $key, array $args ): void {
	$schema = array(
		'type'              => $args['type'],
		'description'       => $args['description'],
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sharkdevelop_sanitize_meta_field',
		'auth_callback'     => static function (): bool {
			return current_user_can( 'edit_posts' );
		},
	);

	if ( isset( $args['items'] ) ) {
		$schema['show_in_rest'] = array(
			'schema' => array(
				'type'  => 'array',
				'items' => $args['items'],
			),
		);
	}

		register_post_meta(
			$post_type,
			$key,
			$schema
		);
}

function sharkdevelop_sanitize_meta_field( mixed $value, string $key ): mixed {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_array( $value ) ) {
		return array_values( array_filter( array_map( 'absint', $value ) ) );
	}

	if ( str_ends_with( $key, '_url' ) ) {
		return esc_url_raw( (string) $value );
	}

	return sanitize_text_field( (string) $value );
}

function sharkdevelop_add_project_homepage_meta_box(): void {
	add_meta_box(
		'sharkdevelop-project-homepage',
		__( 'Homepage card', 'sharkdevelop' ),
		'sharkdevelop_render_project_homepage_meta_box',
		'sd_project',
		'side',
		'high'
	);
}

function sharkdevelop_add_project_terms_meta_box(): void {
	add_meta_box(
		'sharkdevelop-project-terms',
		__( 'Tags and technologies', 'sharkdevelop' ),
		'sharkdevelop_render_project_terms_meta_box',
		'sd_project',
		'normal',
		'default'
	);
}

function sharkdevelop_render_project_terms_meta_box( WP_Post $post ): void {
	sharkdevelop_render_ordered_project_terms_field( $post, 'sd_project_tag' );
	sharkdevelop_render_ordered_project_terms_field( $post, 'sd_project_technology' );
}

function sharkdevelop_enqueue_project_terms_admin_assets( string $hook_suffix ): void {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'sd_project' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script( 'tags-suggest' );
	$script = <<<'JS'
jQuery( function( $ ) {
	$( '#tax-input-sd_project_tag' ).wpTagsSuggest( { taxonomy: 'sd_project_tag' } );
	$( '#tax-input-sd_project_technology' ).wpTagsSuggest( { taxonomy: 'sd_project_technology' } );

	$( document ).on( 'click', '.sharkdevelop-popular-term', function() {
		var $button = $( this );
		var $field = $( '#' + $button.data( 'target' ) );
		var term = $button.data( 'term' ).toString();
		var terms = $field.val().split( ',' ).map( function( value ) {
			return value.trim();
		} ).filter( Boolean );
		var exists = terms.some( function( value ) {
			return value.toLowerCase() === term.toLowerCase();
		} );

		if ( ! exists ) {
			terms.push( term );
			$field.val( terms.join( ', ' ) ).trigger( 'change' );
		}
	} );
} );
JS;

	wp_add_inline_script( 'tags-suggest', $script );
}

function sharkdevelop_render_project_homepage_meta_box( WP_Post $post ): void {
	$featured = (bool) get_post_meta( $post->ID, 'sd_project_featured', true );
	$order = absint( get_post_meta( $post->ID, 'sd_project_home_order', true ) );
	$theme = (string) get_post_meta( $post->ID, 'sd_project_theme', true );
	$summary = (string) get_post_meta( $post->ID, 'sd_project_summary', true );
	$glow = sharkdevelop_normalize_project_glow( (string) get_post_meta( $post->ID, 'sd_project_glow', true ) );

	if ( 'purple' === $theme ) {
		$theme = 'accent';
	}

	wp_nonce_field( 'sharkdevelop_project_homepage', 'sharkdevelop_project_homepage_nonce' );
	?>
	<p>
		<label>
			<input type="checkbox" name="sd_project_featured" value="1" <?php checked( $featured ); ?> />
			<?php esc_html_e( 'Show on homepage', 'sharkdevelop' ); ?>
		</label>
	</p>
	<p>
		<label for="sd_project_home_order"><strong><?php esc_html_e( 'Homepage order', 'sharkdevelop' ); ?></strong></label>
		<input class="widefat" id="sd_project_home_order" min="0" name="sd_project_home_order" type="number" value="<?php echo esc_attr( $order ); ?>" />
	</p>
	<p>
		<label for="sd_project_theme"><strong><?php esc_html_e( 'Card theme', 'sharkdevelop' ); ?></strong></label>
		<select class="widefat" id="sd_project_theme" name="sd_project_theme">
			<option value="dark" <?php selected( $theme, 'dark' ); ?>><?php esc_html_e( 'Dark', 'sharkdevelop' ); ?></option>
			<option value="accent" <?php selected( $theme, 'accent' ); ?>><?php esc_html_e( 'Accent', 'sharkdevelop' ); ?></option>
			<option value="light" <?php selected( $theme, 'light' ); ?>><?php esc_html_e( 'Light', 'sharkdevelop' ); ?></option>
		</select>
	</p>
	<p>
		<label for="sd_project_summary"><strong><?php esc_html_e( 'Short description', 'sharkdevelop' ); ?></strong></label>
		<textarea class="widefat" id="sd_project_summary" name="sd_project_summary" rows="4"><?php echo esc_textarea( $summary ); ?></textarea>
	</p>
	<p>
		<label for="sd_project_glow"><strong><?php esc_html_e( 'Card glow', 'sharkdevelop' ); ?></strong></label>
		<input class="widefat" id="sd_project_glow" name="sd_project_glow" pattern="#[0-9A-Fa-f]{6}" placeholder="#9055FF" type="text" value="<?php echo esc_attr( $glow ); ?>" />
	</p>
	<?php
}

function sharkdevelop_save_project_homepage_meta( int $post_id ): void {
	if (
		! isset( $_POST['sharkdevelop_project_homepage_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sharkdevelop_project_homepage_nonce'] ) ), 'sharkdevelop_project_homepage' ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	$theme = isset( $_POST['sd_project_theme'] ) ? sanitize_key( wp_unslash( $_POST['sd_project_theme'] ) ) : 'dark';
	$glow = isset( $_POST['sd_project_glow'] ) ? sanitize_hex_color( wp_unslash( $_POST['sd_project_glow'] ) ) : null;
	$summary = isset( $_POST['sd_project_summary'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sd_project_summary'] ) ) : '';
	update_post_meta( $post_id, 'sd_project_featured', isset( $_POST['sd_project_featured'] ) ? 1 : 0 );
	update_post_meta( $post_id, 'sd_project_home_order', isset( $_POST['sd_project_home_order'] ) ? absint( $_POST['sd_project_home_order'] ) : 0 );
	update_post_meta( $post_id, 'sd_project_theme', in_array( $theme, array( 'dark', 'accent', 'light' ), true ) ? $theme : 'dark' );
	update_post_meta( $post_id, 'sd_project_summary', $summary );
	update_post_meta( $post_id, 'sd_project_glow', $glow ?: '#9055FF' );
}

function sharkdevelop_normalize_project_glow( string $glow ): string {
	$legacy_colors = array(
		'primary'   => '#2563FF',
		'secondary' => '#0AA7FF',
		'accent'    => '#9055FF',
	);

	if ( isset( $legacy_colors[ $glow ] ) ) {
		return $legacy_colors[ $glow ];
	}

	return sanitize_hex_color( $glow ) ?: '#9055FF';
}
