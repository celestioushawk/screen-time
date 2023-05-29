<?php
/**
 * Template part for displaying movie cast & crew inside another template.
 *
 * @package movie-library-theme
 */

$movie_crew = get_movie_post_meta( get_the_ID(), 'rt-movie-meta-crew-actor', true );
?>
<section class="cast-and-crew">

	<div class="cast-and-crew-header">
		<div class="cast-and-crew-title">
			<span>
				<?php esc_html_e( 'Cast & Crew', 'movie-library' ); ?>
			</span>
		</div>
		<div class="view-more">
			<span>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'rt-person' ) . '?movie_id=' . get_the_ID() ); ?>">
					<?php esc_html_e( 'View More', 'movie-library' ); ?> &rarr;
				</a>
			</span>
		</div>
	</div>

	<div class="cast-and-crew-grid">
		<?php
		if ( $movie_crew ) {
			foreach ( $movie_crew as $crew_person ) :
				$crew_data = get_post( $crew_person );
				?>
				<div class="cast-item">
					<div class="cast-image">
						<?php
						if ( has_post_thumbnail( $crew_person ) ) {
							echo get_the_post_thumbnail( $crew_person );
						} else {
							?>
							<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/images/dummy.png' ); ?>" alt="<?php esc_attr_e( 'default place holder', 'movie-library-theme' ); ?>">
							<?php
						}
						?>
					</div>
					<div class="cast-name">
						<a href="<?php echo esc_url( get_the_permalink( $crew_person ) ); ?>">
							<?php echo esc_html( $crew_data->post_title ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
			<?php
		} else {
			?>
			<span>
				<?php esc_html_e( 'No cast and crew to show!', 'movie-library-theme' ); ?>
			</span>
			<?php
		}
		?>
	</div>

</section>
<?php
