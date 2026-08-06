<?php
get_header();
?>

<main class="site-main site-main--not-found">
	<section class="not-found" aria-labelledby="not-found-title">
		<div class="container not-found__content">
			<p class="eyebrow">404</p>
			<h1 id="not-found-title"><?php esc_html_e( 'Page not found', 'sharkdevelop' ); ?></h1>
			<p><?php esc_html_e( 'The page you are looking for may have moved or no longer exists.', 'sharkdevelop' ); ?></p>
			<div class="not-found__actions">
				<a class="button button--primary has-arrow-icon" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Back to homepage', 'sharkdevelop' ); ?>
				</a>
				<a class="button button--secondary has-arrow-icon" href="<?php echo esc_url( get_post_type_archive_link( 'sd_project' ) ); ?>">
					<?php esc_html_e( 'View projects', 'sharkdevelop' ); ?>
				</a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
