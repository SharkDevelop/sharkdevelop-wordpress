<footer class="site-footer">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<?php $footer_logo_id = sharkdevelop_attachment_id_by_file( '2026/07/sharkdevelop-full-logo.svg' ); ?>
			<?php if ( $footer_logo_id ) : ?>
				<a class="custom-logo-link custom-logo-link--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo sharkdevelop_custom_logo( '', $footer_logo_id ); ?>
				</a>
			<?php endif; ?>
			<p><?php esc_html_e( 'We build digital products that help businesses grow.', 'sharkdevelop' ); ?></p>
		</div>

		<div>
			<h2><?php esc_html_e( 'Services', 'sharkdevelop' ); ?></h2>
			<ul class="site-footer__nav">
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Mobile Apps', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Web Platforms', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Backend & API', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'UI/UX Design', 'sharkdevelop' ); ?></a></li>
			</ul>
		</div>

		<div>
			<h2><?php esc_html_e( 'Company', 'sharkdevelop' ); ?></h2>
			<ul class="site-footer__nav">
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Projects', 'sharkdevelop' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sharkdevelop' ); ?></a></li>
			</ul>
		</div>

		<div>
			<h2><?php esc_html_e( 'Let us talk', 'sharkdevelop' ); ?></h2>
			<ul class="site-footer__nav">
				<li><a href="mailto:hello@sharkdevelop.com">hello@sharkdevelop.com</a></li>
				<li><?php esc_html_e( 'Wilmington, DE, USA', 'sharkdevelop' ); ?></li>
			</ul>
		</div>

		<div class="site-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'sharkdevelop' ); ?></p>
			<div>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'sharkdevelop' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'sharkdevelop' ); ?></a>
			</div>
		</div>
	</div>
</footer>
