<?php
/**
 * Template part for single person popular movies section to display popular movies of the person.
 *
 * @package movie-library-theme
 */

$args                = array(
	'post_type'      => 'rt-movie',
	'posts_per_page' => '3',
	//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	'tax_query'      => array(
		'relation' => 'AND',
		array(
			'taxonomy' => '_rt-movie-person',
			'field'    => 'slug',
			'terms'    => 'person-' . get_the_ID(),
		),
		array(
			'taxonomy' => 'rt-movie-label',
			'field'    => 'slug',
			'terms'    => 'most-popular',
		),
	),
);
$data                = new WP_Query( $args );
$most_popular_movies = $data->posts;
?>
<section class="upcoming-movies">
		<div class="heading"><?php esc_html_e( 'Popular Movies', 'movie-library-theme' ); ?></div>
		<div class="movie-grid">
		<?php foreach ( $most_popular_movies as $most_popular_movie ) : ?>
					<?php
					$movie_genre  = get_the_terms( $most_popular_movie->ID, 'rt-movie-genre' );
					$movie_meta   = get_person_post_meta( $most_popular_movie->ID, 'rt-movie-meta-basic', true );
					$release_date = $movie_meta['rt-movie-meta-basic-release-date'];
					$movie_date   = date_create( $release_date );
					$movie_label  = get_the_terms( $most_popular_movie->ID, 'rt-movie-label' );
					$movie_genres = array();
					foreach ( $movie_genre as $genre ) {
						$movie_genres[] = $genre->name;
					}
					?>
				<div class="movie">
					<div class="movie-poster">
					<?php
					if ( has_post_thumbnail( $most_popular_movie->ID ) ) {
						echo get_the_post_thumbnail( $most_popular_movie->ID );
					} else {
						?>
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/images/dummy.png' ); ?>" alt=" <?php esc_attr_e( 'fallback placeholder image', 'movie-library-theme' ); ?>">
						<?php
					}
					?>
					</div>
					<div class="movie-details">
						<div class="movie-detail-top-row">
							<div class="movie-title">
								<span>
									<?php echo esc_html( $most_popular_movie->post_title ); ?>
								</span>
							</div>
							<div class="movie-genre">
								<span>
									<?php echo esc_html( $movie_meta['rt-movie-meta-basic-runtime'] ); ?>
								</span>
							</div>
						</div>
						<div class="movie-detail-bottom-row">
							<div class="movie-release-date">
								<span>
									<?php echo esc_html( implode( ' &#8226; ', $movie_genres ) ); ?>
								</span>
							</div>
							<div class="movie-age-rating">
								<span>
									<?php echo esc_html( strtok( $release_date, '-' ) ); ?>
								</span>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
		</div>
		<div class="slider-progress-bar">
			<div class="inner-progress-bar"></div>
		</div>
</section>
<?php
