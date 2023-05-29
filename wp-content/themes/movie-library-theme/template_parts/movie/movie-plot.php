<?php
/**
 * Template part for single movie plot section to display movie plot.
 *
 * @package movie-library-theme
 */

?>
<section class="movie-plot">
	<div class="movie-plot-section">
		<div class="plot-title">
			<span>
				<?php esc_html_e( 'Plot', 'movie-library' ); ?>
			</span>
		</div>
		<div class="plot-text">
			<?php
			if ( get_the_content() ) {
				the_content();
			} else {
				esc_html_e( 'Movie plot not available.', 'movie-library' );
			}
			?>
		</div>
	</div>
	<div class="quick-links">
		<div class="quick-link-title">
			<?php esc_html_e( 'Quick Links', 'movie-library' ); ?>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Synopsis', 'movie-library-theme' ); ?>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Cast & Crew', 'movie-library-theme' ); ?>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Snapshots', 'movie-library-theme' ); ?>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Trailer & Clips', 'movie-library-theme' ); ?>
		</div>
		<div class="quick-link">
			<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/arrow-right.svg' ); ?>" alt="<?php esc_attr_e( 'arrow image for menu', 'movie-library-theme' ); ?>"> <?php esc_html_e( 'Reviews', 'movie-library-theme' ); ?>
		</div>
	</div>
	<?php if ( is_active_sidebar( 'ml-movies-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'ml-movies-sidebar' ); ?>
	<?php endif; ?>
</section>
<?php
