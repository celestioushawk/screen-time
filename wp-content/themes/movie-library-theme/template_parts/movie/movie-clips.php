<?php
/**
 * Template part for displaying video clips for both rt-person and rt-movie.
 *
 * @package movie-library
 */

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;


if ( is_singular( Rt_Movie::RT_MOVIE_SLUG ) ) {
	$movie_clips = get_movie_post_meta( get_the_ID(), 'rt-media-meta-videos', true );
} elseif ( is_singular( Rt_Person::RT_PERSON_SLUG ) ) {
	$movie_clips = get_person_post_meta( get_the_ID(), 'rt-media-meta-videos', true );
}
?>

<section class="trailers-and-clips">
		<div class="trailer-and-clips-header">
		<?php
		if ( get_post_type() === 'rt-movie' ) {
			?>
			<span>
				<?php esc_html_e( 'Trailer & Clips', 'movie-library' ); ?>
			</span>
			<?php
		} else {
			?>
			<span>
				<?php esc_html_e( 'Videos', 'movie-library' ); ?>
			</span>
			<?php
		}
		?>
		</div>
		<div class="trailer-and-clips-flex">
		<?php
		if ( $movie_clips ) {
			foreach ( $movie_clips as $clip ) {
				?>
				<div class="video-container">
					<video src="<?php echo esc_url( wp_get_attachment_url( $clip ) ); ?>" class="trailer" onclick="play(this)" width="384" height="246"></video>
					<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/play-circle-1.svg' ); ?>" alt="<?php esc_attr_e( 'video play button image', 'movie-library-theme' ); ?>" class="play-btn">
				</div>
				<?php
			}
		} else {
			?>
			<span>
				<?php esc_html_e( 'No clips to show!', 'movie-library' ); ?>
			</span>
			<?php
		}
		?>
		</div>
</section>
<?php
