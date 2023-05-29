<?php
/**
 * Archive page template for archive rt-movie to display multiple posts in the same page.
 *
 * @package movie-library-theme
 */

get_header();
$upcoming_movie_ids   = array();
$tax_query[]          = array(
	'taxonomy' => 'rt-movie-label',
	'field'    => 'slug',
	'terms'    => 'upcoming-movies',
);
$upcoming_movies_data = new WP_Query(
	[
		'post_type'              => 'rt-movie',
		'posts_per_page'         => 6,
		//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		'tax_query'              => $tax_query,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_term_meta_cache' => false,
	]
);
$upcoming_movies      = $upcoming_movies_data->posts;

$trending_movie_ids = array();
$tax_query          = array();
$tax_query[]        = array(
	'taxonomy' => 'rt-movie-label',
	'field'    => 'slug',
	'terms'    => 'trending-now',
);
$trending_data      = new WP_Query(
	[
		'post_type'              => 'rt-movie',
		'posts_per_page'         => 6,
		//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		'tax_query'              => $tax_query,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_term_meta_cache' => false,
	]
);
$trending_movies    = $trending_data->posts;

$recent_query_movies = array_slice( $wp_query->posts, 0, 4 );
foreach ( $recent_query_movies as $query_movie ) {
	$recent_movie_ids[] = $query_movie->ID;
}

?>
<main>
	<div class="hero-slider">
		<?php foreach ( $recent_query_movies as $recent_movie ) : ?>

			<?php
			$movie_title        = $recent_movie->post_title;
			$movie_desc         = $recent_movie->post_excerpt;
			$movie_meta_basic   = get_movie_post_meta( $recent_movie->ID, 'rt-movie-meta-basic', true );
			$movie_runtime      = $movie_meta_basic['rt-movie-meta-basic-runtime'];
			$movie_release_date = $movie_meta_basic['rt-movie-meta-basic-release-date'];
			$movie_genre        = get_the_terms( $recent_movie->ID, 'rt-movie-genre' );
			?>


			<div class="slide" style="background: url(<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id( $recent_movie->ID ) ) ); ?>); background-repeat: no-repeat; background-size: cover;">
				<div class="hero-movie-stats">
					<h1>
						<a href="<?php echo esc_url( get_the_permalink( $recent_movie->ID ) ); ?>">
							<?php echo esc_html( $movie_title ); ?>
						</a>
					</h1>
					<p class="movie-desc">
						<?php
						if ( $movie_desc ) {
							echo esc_html( $movie_desc );
						} else {
							esc_html_e( 'Movie summary not available!', 'movie-library' );
						}
						?>
					</p>
					<div class="movie-meta-basic">
						<span class="release-year">
							<?php
							if ( $movie_meta_basic['rt-movie-meta-basic-release-date'] ) {
								echo esc_html( strtoupper( strtok( $movie_meta_basic['rt-movie-meta-basic-release-date'], '-' ) ) );
							} else {
								esc_html_e( 'Movie release date not available!', 'movie-library' );
							}
							?>
						</span>
						<div class="divider"></div>
						<span class="age-rating">
							<?php esc_html_e( 'PG-13', 'movie-library-theme' ); ?>
						</span>
						<div class="divider"></div>
						<span class="runtime">
							<?php
							if ( $movie_meta_basic['rt-movie-meta-basic-runtime'] ) {
								echo esc_html( strtoupper( $movie_meta_basic['rt-movie-meta-basic-runtime'] ) );
							} else {
								esc_html_e( 'Movie runtime not available!', 'movie-library' );
							}
							?>
						</span>
					</div>
					<div class="movie-tags">
						<?php foreach ( $movie_genre as $genre ) : ?>
							<div class="movie-tag">
								<a href="<?php echo esc_url( get_term_link( $genre->term_id ) ); ?>">
									<?php echo esc_html( $genre->name ); ?>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>


		<?php endforeach; ?>

	</div>
	<div class="slider-dots">
		<?php
		echo wp_kses(
			str_repeat( '<span class="dot"></span>', count( $recent_query_movies ) ),
			array(
				'span' => [
					'class' => [],
				],
			)
		);
		?>
	</div>

	<section class="upcoming-movies">
		<div class="heading">
			<span>
				<?php esc_html_e( 'Upcoming Movies', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="movie-grid movie-grid-one">
			<?php foreach ( $upcoming_movies as $upcoming_movie ) : ?>
				<?php
				$movie_genre  = get_the_terms( $upcoming_movie->ID, 'rt-movie-genre' );
				$movie_meta   = get_movie_post_meta( $upcoming_movie->ID, 'rt-movie-meta-basic', true );
				$release_date = $movie_meta['rt-movie-meta-basic-release-date'];
				$movie_date   = date_create( $release_date );
				$movie_label  = get_the_terms( $upcoming_movie->ID, 'rt-movie-label' );
				?>
			<div class="movie">
				<div class="movie-poster">
					<?php
					if ( has_post_thumbnail( $upcoming_movie->ID ) ) {
						echo get_the_post_thumbnail( $upcoming_movie->ID );
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
							<a href="<?php echo esc_url( get_the_permalink( $upcoming_movie->ID ) ); ?>">
								<?php echo esc_html( $upcoming_movie->post_title ); ?>
							</a>
						</div>
						<div class="movie-genre">
							<?php
							if ( $movie_genre ) {
								?>
									<a href="<?php echo esc_url( get_term_link( $movie_genre[0]->term_id ) ); ?>">
										<?php echo esc_html( $movie_genre[0]->name ); ?>
									</a>
								<?php
							} else {
								?>
									<span>
										<?php esc_attr_e( 'N/A', 'movie-library' ); ?>
									</span>
								<?php
							}
							?>
						</div>
					</div>
					<div class="movie-detail-bottom-row">
						<div class="movie-release-date">
							<span>
								<?php esc_html_e( 'Release', 'movie-library-theme' ); ?>: <?php echo esc_html( $movie_date->format( 'd M Y' ) ); ?>
							</span>
						</div>
						<div class="movie-age-rating">
							<span>
								<?php esc_html_e( 'PG-13', 'movie-library-theme' ); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="slider-progress-bar">
			<div class="inner-progress-bar inner-progress-bar-one"></div>
		</div>
	</section>


	<section class="upcoming-movies">
		<div class="heading">
			<span>
				<?php esc_html_e( 'Trending Now', 'movie-library-theme' ); ?>
			</span>
		</div>
		<div class="movie-grid movie-grid-two">
			<?php foreach ( $trending_movies as $trending_movie ) : ?>
				<?php
				$trending_movie_name         = $trending_movie->post_title;
				$trending_movie_meta         = get_movie_post_meta( $trending_movie->ID, 'rt-movie-meta-basic', true );
				$trending_movie_runtime      = $trending_movie_meta['rt-movie-meta-basic-runtime'];
				$trending_movie_genre        = get_the_terms( $trending_movie->ID, 'rt-movie-genre' );
				$trending_movie_release_date = $trending_movie_meta['rt-movie-meta-basic-release-date'];
				?>
			<div class="movie">
				<div class="movie-poster">
					<?php echo get_the_post_thumbnail( $trending_movie->ID ); ?>
				</div>
				<div class="movie-details">
					<div class="movie-detail-top-row">
						<div class="movie-title">
							<a href="<?php echo esc_url( get_the_permalink( $trending_movie->ID ) ); ?>">
								<?php echo esc_html( $trending_movie_name ); ?>
							</a>
						</div>
						<div class="movie-genre">
							<span>
								<?php echo esc_html( strtoupper( $trending_movie_runtime ) ); ?>
							</span>
						</div>
					</div>
					<div class="movie-detail-bottom-row">
						<div class="movie-release-date">
							<a href="<?php echo esc_url( get_term_link( $trending_movie_genre[0]->term_id ) ); ?>">
								<?php echo esc_html( $trending_movie_genre[0]->name ); ?>
							</a>
						</div>
						<div class="movie-age-rating">
							<span>
								<?php echo esc_html( strtok( $trending_movie_release_date, '-' ) ); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="slider-progress-bar">
			<div class="inner-progress-bar inner-progress-bar-two"></div>
		</div>
	</section>

</main>
<?php
get_footer();
