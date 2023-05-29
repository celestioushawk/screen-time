<?php
/**
 * Archive page for the rt-person post type to list the names and information about the crew stored inside the databtase.
 *
 * @package movie-library-theme
 */

get_header();

$movie_id_present = false;
$data_from_db     = array();

//phpcs:ignore
if ( isset( $_GET['movie_id'] ) ) {
	//phpcs:ignore
	$movie_id               = sanitize_text_field( wp_unslash( $_GET['movie_id'] ) );
	$movie_id_present       = true;
	$movie_actors_ids       = get_person_post_meta( $movie_id, 'rt-movie-meta-crew-actor', true );
	$movie_actors_character = get_person_post_meta( $movie_id, 'rt-movie-character', true );
	$data_from_db           = $movie_actors_ids;
} else {
	$query_people = array_slice( $wp_query->posts, 0, 5 );
	foreach ( $query_people as $person ) {
		$data_from_db[] = $person->ID;
	}
}
?>

<section class="full-cast-crew">
	<div class="full-cast-crew-header">
		<span>
			<?php esc_html_e( 'Cast & Crew', 'movie-library' ); ?>
		</span>
	</div>
	<div class="full-cast-crew-grid">
		<?php foreach ( $data_from_db as $actor_id ) : ?>
			<?php
			$movie_actor_data       = get_post( $actor_id );
			$person_basic_meta_data = get_person_post_meta( $actor_id, 'rt-person-meta-basic', true );
			$person_birth_date      = date_create( $person_basic_meta_data['rt-person-meta-basic-birth-date'] );
			$birth_date             = $person_birth_date->format( 'j F Y' );
			if ( $movie_id_present ) {
				$movie_actors_character = get_person_post_meta( $movie_id, 'rt-movie-character', true );
			}
			?>
			<div class="cast-crew-grid">
				<div class="crew-image">
				<?php
				if ( has_post_thumbnail( $actor_id ) ) {
					echo get_the_post_thumbnail( $actor_id );
				} else {
					?>
					<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/images/dummy.png' ); ?>" alt="<?php esc_attr_e( 'default place holder image', 'movie-library-theme' ); ?>">
					<?php
				}
				?>
				</div>
				<div class="crew-item-main-info">
					<p class="crew-name">
						<a href="<?php echo esc_url( get_the_permalink( $actor_id ) ); ?>"><?php echo esc_html( $movie_actor_data->post_title ); ?></a>
						<span>
						<?php
						if ( $movie_id_present ) {
							echo '(' . esc_html( $movie_actors_character[ $actor_id ] ) . ')';
						}
						?>
					</span>
					</p>
					<p class="crew-birth-date">
						<?php esc_html_e( 'Born', 'movie-library-theme' ); ?> - <?php echo esc_html( $birth_date ); ?>
					</p>
				</div>
				<div class="crew-item-summary">
					<p class="crew-person-summary">
						<?php echo esc_html( get_the_excerpt( $actor_id ) ); ?>
					</p>
					<a href="<?php echo esc_url( get_the_permalink( $actor_id ) ); ?>">
						<?php esc_html_e( 'Learn More', 'movie-library-theme' ); ?> &rarr;
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<div class="load-more-container">
	<div class="load-more-btn">
		<span>
			<?php esc_html_e( 'Load More', 'movie-library-theme' ); ?>
		</span>
	</div>
</div>
<?php
get_footer();
