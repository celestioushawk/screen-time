<?php
/**
 * Template part for single movie hero section to display movie poster and its details.
 *
 * @package movie-library-theme
 */

$directors        = get_movie_post_meta( get_the_ID(), 'rt-movie-meta-crew-director', true );
$movie_meta_basic = get_movie_post_meta( get_the_ID(), 'rt-movie-meta-basic', true );
$movie_label      = get_the_terms( get_the_ID(), 'rt-movie-label' );
$movie_tags       = get_the_terms( get_the_ID(), 'rt-movie-tag' );
$movie_genre      = get_the_terms( get_the_ID(), 'rt-movie-genre' );
?>

<section class="hero-movie">
		<div class="left-item">
			<?php
			if ( has_post_thumbnail() ) {
				echo get_the_post_thumbnail();
			} else {
				?>
				<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/images/dummy.png' ); ?>" alt="<?php esc_attr_e( 'default place holder', 'movie-library-theme' ); ?>">
				<?php
			}
			?>
		</div>
		<div class="right-item">

			<div class="movie-heading">
				<span>
					<?php the_title(); ?>
				</span>
			</div>

			<div class="movie-stats">
				<div class="movie-rating">
					<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/star.svg' ); ?>" alt="<?php esc_attr_e( 'star image for rating', 'movie-library-theme' ); ?>">
					<?php
					if ( $movie_meta_basic['rt-movie-meta-basic-rating'] ) {
						echo esc_html( $movie_meta_basic['rt-movie-meta-basic-rating'] );
						?>
						/10
						<?php
					} else {
						esc_html_e( 'N/A', 'movie-library-theme' );
					}
					?>
				</div>
				<div class="divider"></div>
				<div class="release-year">
					<span>
						<?php
						if ( $movie_meta_basic['rt-movie-meta-basic-release-date'] ) {
							echo esc_html( strtoupper( strtok( $movie_meta_basic['rt-movie-meta-basic-release-date'], '-' ) ) );
						} else {
							esc_html_e( 'Movie release date not available!', 'movie-library' );
						}
						?>
					</span>
				</div>
				<div class="divider"></div>
				<div class="age-rating">
					<span>
						<?php esc_html_e( 'PG-13', 'movie-library' ); ?>
					</span>
				</div>
				<div class="divider"></div>
				<div class="runtime">
					<span>
						<?php
						if ( $movie_meta_basic['rt-movie-meta-basic-runtime'] ) {
							if ( 'minutes' === get_theme_mod( 'ml_time_format' ) ) {
								$runtime = $movie_meta_basic['rt-movie-meta-basic-runtime'] . ' ' . __( 'minutes', 'movie-library' );
								echo esc_html( $runtime );
							} else {
								printf( '%.0fH %.0fM', esc_html( ( (float) $movie_meta_basic['rt-movie-meta-basic-runtime'] ) / 60 ), esc_html( ( (float) $movie_meta_basic['rt-movie-meta-basic-runtime'] ) % 60 ) );
							}
						} else {
							esc_html_e( 'Movie runtime not available!', 'movie-library' );
						}
						?>
					</span>
				</div>
			</div>

			<div class="movie-summary">
				<?php
				if ( has_excerpt() ) {
					the_excerpt();
				} else {
					esc_html_e( 'Movie summary not available!', 'movie-library' );
				}
				?>
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

			<div class="movie-directors">
				<span class="director-title">
					<?php esc_html_e( 'Directors', 'movie-library-theme' ); ?>:
				</span>
				<?php
				if ( $directors ) {
					foreach ( $directors as $director ) :
						$director_data = get_post( $director );
						$director_name = $director_data->post_title;
						?>
					<div class="movie-director">
						<span>
							<?php echo esc_html( $director_name ); ?>
						</span>
					</div>
					<div class="divider"></div>
						<?php
						endforeach;
				} else {
					esc_html_e( 'No director data.', 'movie-library-theme' );
				}
				?>
			</div>

			<div class="movie-trailer">
				<div class="watch-trailer">
					<div class="play-btn">
						<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/play-circle-1.svg' ); ?>" alt="<?php esc_attr_e( 'watch trailer icon image', 'movie-library-theme' ); ?>">
					</div>
					<span>
						<?php esc_html_e( 'Watch Trailer', 'movie-library-theme' ); ?>
					</span>
				</div>
			</div>

		</div>

	</section>
<?php
