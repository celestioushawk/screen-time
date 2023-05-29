<?php
/**
 * Template part for single person about section to display info about the person.
 *
 * @package movie-library-theme
 */

?>
<section class="movie-plot">
	<div class="movie-plot-section">
		<div class="plot-title">
			<span>
				<?php esc_html_e( 'About', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="plot-text">
			<?php
			if ( get_the_content() ) {
				the_content();
			} else {
				esc_html_e( 'Person biography not available.', 'movie-library' );
			}
			?>
		</div>
	</div>
	<div class="quick-links">
		<div class="quick-link-title">
			<span>
				<?php esc_html_e( 'Quick Links', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu option', 'movie-library-theme' ); ?>">
			<span>
				<?php esc_html_e( 'About', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu option', 'movie-library-theme' ); ?>">
			<span>
				<?php esc_html_e( 'Family', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu option', 'movie-library-theme' ); ?>">
			<span>
				<?php esc_html_e( 'Snapshots', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu option', 'movie-library-theme' ); ?>">
			<span>
				<?php esc_html_e( 'Videos', 'movie-library-theme' ); ?>
			</span>
		</div>
	</div>
	<?php if ( is_active_sidebar( 'ml-person-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'ml-person-sidebar' ); ?>
	<?php endif; ?>
</section>
<?php
