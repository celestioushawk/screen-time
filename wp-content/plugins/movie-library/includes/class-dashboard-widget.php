<?php
/**
 * Create dashboard widgets for showing different movies according to category and display movies from an
 * external API using the iMDB API.
 *
 * @package movie-library
 */

namespace Includes\Class_Dashboard_Widget;

use Includes\Class_Rt_Movie\Rt_Movie;
use WP_Error;
use WP_Query;

/**
 * Dashboard widget class for defining and adding dashboard widgets for top rated and recent movies from the
 * database and upcoming movies from imdb API.
 */
class Dashboard_Widget {

	/**
	 * Constructor function to hook the dashboard function to the wp_dashboard_setup action hook.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'ml_movies_dashboard' ) );
	}
	/**
	 * Define and register dashboard widgets for top rated and recent movies from database widget and
	 * upcoming movies dashboard widget from imdb API.
	 *
	 * @return void
	 */
	public function ml_movies_dashboard() {

		// Register recent and top rated movies dashboard widget.
		wp_add_dashboard_widget(
			'ml_movies_from_db',
			esc_html__( 'Recent and Top Rated Movies', 'movie-library' ),
			array( $this, 'ml_db_movies_dashboard_render' ),
		);

		// Register iMDb upcoming movies dashboard widget.
		wp_add_dashboard_widget(
			'ml_movies_from_imdb',
			esc_html__( 'IMDb Upcoming Movies', 'movie-library' ),
			array( $this, 'ml_imdb_movies_dashboard_render' ),
		);
	}
	/**
	 * Function to render the top movies and recent movies from database dashboard widget.
	 *
	 * @return void
	 */
	public function ml_db_movies_dashboard_render() {

		// WP Query to fetch movies with the most-recent movie label.
		$recent_movie_args = array(
			'post_type'      => Rt_Movie::RT_MOVIE_SLUG,
			'posts_per_page' => 4,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => Rt_Movie::RT_MOVIE_LABEL,
					'field'    => 'slug',
					'terms'    => 'most-recent',
				),
			),
		);

		$recent_movies = new WP_Query( $recent_movie_args );

		// WP Query to fetch movies with top-rated movie label.
		$top_rated_movies_args = array(
			'post_type'      => Rt_Movie::RT_MOVIE_SLUG,
			'posts_per_page' => 4,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => Rt_Movie::RT_MOVIE_LABEL,
					'field'    => 'slug',
					'terms'    => 'top-rated',
				),
			),
		);

		$top_rated_movies = new WP_Query( $top_rated_movies_args );

		// HTML markup to render list of most recent and top rated movies from the database.
		?>
		<h2>
			<?php esc_html_e( 'Recent Movies', 'movie-library' ); ?>
		</h2>
		<div class="movies-container">
		<?php
		foreach ( $recent_movies->posts as $recent_movie ) :
			?>
				<div class="movie-item">
					<?php echo get_the_post_thumbnail( $recent_movie->ID ); ?>
					<h3>
						<a href="<?php esc_url( the_permalink( $recent_movie->ID ) ); ?>">
							<?php echo esc_html( $recent_movie->post_title ); ?>
						</a>
					</h3>
				</div>
			<?php
		endforeach;
		?>
		</div>

		<h2>
			<?php esc_html_e( 'Top Rated Movies', 'movie-library' ); ?>
		</h2>
		<div class="movies-container">
		<?php
		foreach ( $top_rated_movies->posts as $top_movie ) :
			?>
				<div class="movie-item">
					<?php echo get_the_post_thumbnail( $top_movie->ID ); ?>
					<h3>
						<a href="<?php echo esc_url( get_post_permalink( $top_movie->ID ) ); ?>">
							<?php echo esc_html( $top_movie->post_title ); ?>
						</a>
					</h3>
				</div>
			<?php
		endforeach;
		?>
		</div>
		<?php
	}

	/**
	 * Function to render list of upcoming movies and using iMDb API to fetch those movies using an
	 * API call and implement transient.
	 *
	 * @return void
	 */
	public function ml_imdb_movies_dashboard_render() {

		// Get the API Key from the wp_options table.
		$api_key = get_option( 'ml_api_key_field' );

		if ( ! $api_key ) {
			?>
				<h3>
					<?php esc_html_e( 'No API Key provided!', 'movie-library' ); ?>
				</h3>
			<?php
			return;
		}

		// Check for existing transient and set if not available.
		$imdb_movies_transient = get_transient( 'imdb_movies' );

		if ( false === $imdb_movies_transient ) {

			$imdb_response = wp_remote_get( 'https://imdb-api.com/en/API/ComingSoon/' . $api_key );

			if ( $imdb_response instanceof WP_Error ) {
				echo esc_html_e( 'Error recieving data from the API. Please check your API Key.', 'movie-library' );
				return;
			}

			$imdb_body = wp_remote_retrieve_body( $imdb_response );

			$decoded_json = json_decode( $imdb_body, false );

			$movies_list = array_slice( $decoded_json->items, 0, 10 );

			set_transient( 'imdb_movies', $movies_list, 4 * HOUR_IN_SECONDS );

			$imdb_movies_transient = $movies_list;
		}
		?>
		<div class="imdb-movies-container">
		<?php
		// HTML Markup for rendering the imdb upcoming movies.
		if ( is_array( $imdb_movies_transient ) ) {
			foreach ( $imdb_movies_transient as $movie ) :
				?>
				<div class="imdb-movie">
					<img src="<?php echo esc_url( $movie->image ); ?>" alt="<?php esc_attr_e( 'movie poster', 'movie-library' ); ?>" class="imdb-movie-image">
					<h3 class="movie-title">
						<a href="<?php esc_url( printf( 'https://www.imdb.com/title/%s', esc_attr( $movie->id ) ) ); ?>/">
							<?php
								//phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								echo esc_html( $movie->fullTitle );
							?>
						</a>
					</h3>
				</div>
				<?php
			endforeach;
		} else {
			echo esc_html_e( 'Invalid Data.', 'movie-library' );
		}
		?>
		</div>
		<?php
	}
}
