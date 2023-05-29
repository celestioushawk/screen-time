<?php
/**
 * Template part for single movie snapshots section to display movie photos uploaded thorough the metaboxes.
 *
 * @package movie-library-theme
 */

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;

$snapshots = array();
if ( is_singular( Rt_Movie::RT_MOVIE_SLUG ) ) {
	$snapshots = get_movie_post_meta( get_the_ID(), 'rt-media-meta-images', true );
} elseif ( is_singular( Rt_Person::RT_PERSON_SLUG ) ) {
	$snapshots = get_person_post_meta( get_the_ID(), 'rt-media-meta-images', true );
}
?>

<section class="masonry">
	<div class="masonry-header">
		<div class="masonry-title">
			<span>
				<?php esc_html_e( 'Snapshots', 'movie-library-theme' ); ?>
			</span>
		</div>
		<?php
		if ( $snapshots ) {
			?>
			<div class="masonry-images">
				<?php foreach ( $snapshots as $snapshot ) : ?>
				<img src="<?php echo esc_url( wp_get_attachment_url( $snapshot ) ); ?>" alt="<?php esc_attr_e( 'movie snapshot', 'movie-library-theme' ); ?>">
				<?php endforeach; ?>
			</div>
			<?php
		} else {
			?>
			<span>
				<?php esc_html_e( 'No snapshots to show!', 'movie-library-theme' ); ?>
			</span>
			<?php
		}
		?>
	</div>
</section>
<?php
