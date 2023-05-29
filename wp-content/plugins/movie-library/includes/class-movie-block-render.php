<?php
/**
 * Render function for custom single movie and movie list block.
 *
 * @package movie-library
 */

namespace Includes\Class_Movie_Block_Render;

define( 'BUILD_FOLDER', __DIR__ . '/../blocks/custom-blocks/build' );

use Includes\Class_Rt_Movie\Rt_Movie;

/**
 * Class to define render functions for single movie and movie list block.
 */
class Movie_Block_Render {
	/**
	 * Enqueue the render function for blocks on init hook.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_block_movies_block_init' ) );
	}
	/**
	 * Render callback function for movie list custom block.
	 *
	 * @param array $attributes The arrtributes array from block.
	 * @return string The HTML output.
	 */
	public function block_movie_list_render_callback( $attributes ) {

		$tax_query_args = array();

		if ( empty( $attributes ) ) {
			return __( 'Please select a filter.', 'movie-library' );
		}

		if ( ! empty( $attributes['movieGenre'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_GENRE,
				'field'    => 'term_id',
				'terms'    => sanitize_text_field( $attributes['movieGenre'] ),
			);
		}

		if ( ! empty( $attributes['movieLabel'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_LABEL,
				'field'    => 'term_id',
				'terms'    => sanitize_text_field( $attributes['movieLabel'] ),
			);
		}

		if ( ! empty( $attributes['movieLanguage'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_LANGUAGE,
				'field'    => 'term_id',
				'terms'    => sanitize_text_field( $attributes['movieLanguage'] ),
			);
		}

		if ( ! empty( $attributes['movieDirector'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => '_rt-movie-person',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( "person-{$attributes['movieDirector']}" ),
			);
		}

		$movie_query_args = array(
			'post_type'              => Rt_Movie::RT_MOVIE_SLUG,
			'posts_per_page'         => ! empty( $attributes['movieCount'] ) ? absint( $attributes['movieCount'] ) : '',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'              => array(
				array(
					$tax_query_args,
				),
			),
		);

		$result_movies_query = new \WP_Query( $movie_query_args );

		ob_start();

		if ( $result_movies_query->have_posts() ) {
			?>
			<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
			<?php
			while ( $result_movies_query->have_posts() ) :
				$result_movies_query->the_post();
				$director_names   = array();
				$actor_names      = array();
				$movie_post_meta  = get_movie_post_meta( get_the_ID() );
				$movie_post_meta  = array_combine( array_keys( $movie_post_meta ), array_column( $movie_post_meta, '0' ) );
				$movie_directors  = maybe_unserialize( $movie_post_meta['rt-movie-meta-crew-director'] );
				$movie_actors     = maybe_unserialize( $movie_post_meta['rt-movie-meta-crew-actor'] );
				$movie_basic_meta = maybe_unserialize( $movie_post_meta['rt-movie-meta-basic'] );

				if ( is_array( $movie_directors ) ) {
					foreach ( $movie_directors as $director_id ) {
						$name             = get_post( $director_id );
						$director_names[] = $name->post_title;
					}
				}

				if ( is_array( $movie_actors ) ) {
					foreach ( $movie_actors as $actor_id ) {
						$name          = get_post( $actor_id );
						$actor_names[] = $name->post_title;
					}
				}
				?>
						<div class="movie-container">
							<div class="movie-poster">
								<?php the_post_thumbnail(); ?>
							</div>
							<div class="movie-details">
								<a href="<?php esc_url( the_permalink() ); ?>">
									<h3 class="movie-title">
										<?php the_title(); ?>
									</h3>
								</a>
								<p class="movie-release-date">
									<span>
										<?php echo esc_html_e( 'Release Date: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( $movie_basic_meta['rt-movie-meta-basic-release-date'] ?? '' ); ?>
								</p>
								<p class="movie-directors">
									<span>
										<?php echo esc_html_e( 'Directors: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( implode( ',', $director_names ) ); ?>
								</p>
								<p class="movie-actors">
									<span>
										<?php echo esc_html_e( 'Actors: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( implode( ', ', array_slice( $actor_names, 0, 2 ) ) ); ?>
								</p>
							</div>
						</div>
				<?php
			endwhile;
			?>
			</div>
			<?php
		} else {
			echo esc_html_e( 'No Posts to Show', 'movie-library' );
		}

		wp_reset_postdata();

		return ob_get_clean();

	}
	/**
	 * Render callback function for single movie custom block.
	 *
	 * @param array $attributes The attributes array recieved from block.
	 * @return string The HTML output.
	 */
	public function block_single_movie_render_callback( $attributes ) {

		if ( empty( $attributes['movieId'] ) ) {
			return __( 'Search for a movie.', 'movie-library' );
		}

		$movie_query_args = array(
			'post_type'              => Rt_Movie::RT_MOVIE_SLUG,
			'p'                      => absint( $attributes['movieId'] ),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		);

		$result_movies_query = new \WP_Query( $movie_query_args );

		ob_start();

		if ( $result_movies_query->have_posts() ) {
			?>
			<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
			<?php
			while ( $result_movies_query->have_posts() ) :
				$result_movies_query->the_post();
				$director_names   = array();
				$actor_names      = array();
				$movie_post_meta  = get_movie_post_meta( get_the_ID() );
				$movie_post_meta  = array_combine( array_keys( $movie_post_meta ), array_column( $movie_post_meta, '0' ) );
				$movie_directors  = maybe_unserialize( $movie_post_meta['rt-movie-meta-crew-director'] );
				$movie_actors     = maybe_unserialize( $movie_post_meta['rt-movie-meta-crew-actor'] );
				$movie_basic_meta = maybe_unserialize( $movie_post_meta['rt-movie-meta-basic'] );

				if ( is_array( $movie_directors ) ) {
					foreach ( $movie_directors as $director_id ) {
						$name             = get_post( $director_id );
						$director_names[] = $name->post_title;
					}
				}

				if ( is_array( $movie_actors ) ) {
					foreach ( $movie_actors as $actor_id ) {
						$name          = get_post( $actor_id );
						$actor_names[] = $name->post_title;
					}
				}
				?>
						<div class="movie-container">
							<div class="movie-poster">
								<?php the_post_thumbnail(); ?>
							</div>
							<div class="movie-details">
								<a href="<?php esc_url( the_permalink() ); ?>">
									<h3 class="movie-title">
										<?php the_title(); ?>
									</h3>
								</a>
								<p class="movie-release-date">
									<span>
										<?php echo esc_html_e( 'Release Date: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( $movie_basic_meta['rt-movie-meta-basic-release-date'] ?? '' ); ?>
								</p>
								<p class="movie-directors">
									<span>
										<?php echo esc_html_e( 'Directors: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( implode( ',', $director_names ) ); ?>
								</p>
								<p class="movie-actors">
									<span>
										<?php echo esc_html_e( 'Actors: ', 'movie-library' ); ?>
									</span>
									<?php echo esc_html( implode( ', ', array_slice( $actor_names, 0, 2 ) ) ); ?>
								</p>
							</div>
						</div>
				<?php
			endwhile;
			?>
			</div>
			<?php
		} else {
			echo esc_html_e( 'No Posts to Show', 'movie-library' );
		}

		wp_reset_postdata();

		return ob_get_clean();

	}
	/**
	 * Function to enqueue custom gutenberg block
	 *
	 * @return void
	 */
	public function create_block_movies_block_init() {
		// Register movies list block.
		register_block_type(
			BUILD_FOLDER . '/movie-list',
			array(
				'render_callback' => array( $this, 'block_movie_list_render_callback' ),
			),
		);
		// Register single movie block.
		register_block_type(
			BUILD_FOLDER . '/single-movie',
			array(
				'render_callback' => array( $this, 'block_single_movie_render_callback' ),
			)
		);
	}

}
