<?php
/**
 * Template part for footer section to display to display navigation menu.
 *
 * @package movie-library-theme
 */

?>
<footer>
		<div class="footer-content">

			<div class="footer-content-left">
				<div class="footer-logo">
					<?php esc_html_e( 'Screen', 'movie-library' ); ?> <span><?php esc_html_e( 'Time', 'movie-library-theme' ); ?></span>
				</div>
				<div class="footer-links">
					<div class="footer-links-title">
						<?php esc_html_e( 'Follow Us', 'movie-library-theme' ); ?>
					</div>
					<div class="social-links">
						<div class="facebook link">
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/facebook.svg' ); ?>" alt="<?php esc_attr_e( 'facebook logo', 'movie-library-theme' ); ?>">
						</div>
						<div class="twitter link">
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/twitter.svg' ); ?>" alt="<?php esc_attr_e( 'twitter logo', 'movie-library-theme' ); ?>">
						</div>
						<div class="youtube link">
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/YouTube.svg' ); ?>" alt="<?php esc_attr_e( 'youtube logo', 'movie-library-theme' ); ?>">
						</div>
						<div class="instagram link">
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/instagram.svg' ); ?>" alt="<?php esc_attr_e( 'instagram logo', 'movie-library-theme' ); ?>">
						</div>
						<div class="rss link">
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/RSS.svg' ); ?>" alt="<?php esc_attr_e( 'rss logo', 'movie-library-theme' ); ?>">
						</div>
					</div>
				</div>
			</div>

			<div class="footer-content-right">
				<div class="list">
					<h4><?php esc_html_e( 'Company', 'movie-library-theme' ); ?></h4>
				<ul>
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'company_footer_menu',
						)
					);
					?>
				</ul>
				</div>
				<div class="list">
					<h4><?php esc_html_e( 'Explore', 'movie-library-theme' ); ?></h4>
				<ul>
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'explore_footer_menu',
							)
						);
						?>
				</ul>
				</div>
			</div>

		</div>
		<hr>
		<div class="footer-stamp">
			<p>© <?php esc_html_e( '2022 Lifestyle Magazine. All Rights Reserved. Terms of Service  |  Privacy Policy', 'movie-library-theme' ); ?></p>
		</div>
		<?php wp_footer(); ?>
	</footer>

</body>
</html>
<?php
