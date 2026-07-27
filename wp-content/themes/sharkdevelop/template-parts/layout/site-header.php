<header class="site-header">
	<div class="container site-header__inner">
		<?php if ( sharkdevelop_custom_logo_id() ) : ?>
			<a class="custom-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php echo sharkdevelop_custom_logo(); ?>
			</a>
		<?php else : ?>
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="site-logo__mark" aria-hidden="true">SD</span>
				<span class="site-logo__text">
					<span>Shark</span>
					<span>Develop</span>
				</span>
			</a>
		<?php endif; ?>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'sharkdevelop' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => false,
					'menu_class'     => 'site-nav__list',
				)
			);
			?>
			<?php if ( ! has_nav_menu( 'primary' ) ) : ?>
				<ul class="site-nav__list">
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'sharkdevelop' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Projects', 'sharkdevelop' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'sharkdevelop' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'sharkdevelop' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sharkdevelop' ); ?></a></li>
				</ul>
			<?php endif; ?>
		</nav>

		<div class="site-header__actions">
			<a class="button button--primary has-arrow-icon" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<?php esc_html_e( 'Contact', 'sharkdevelop' ); ?>
			</a>
			<button class="icon-button" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'sharkdevelop' ); ?>">
				<span></span>
			</button>
		</div>
	</div>
</header>
