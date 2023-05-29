<?php
/**
 * Create and register a movie widget to display a list of movies based on options taken from
 * the user.
 *
 * @package movie-library
 */

namespace Widgets\Class_Movie_Widget;

use WP_Query;
use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;

/**
 * Class to implement a custom widget to display a list of movies based on input
 * taken from a user. This class extends the WP_Widget and overrides its methods
 * to imlplement a widget.
 */
class Movie_Widget extends \WP_Widget {
	/**
	 * Call the parent class contructor and register a movie widget.
	 */
	public function __construct() {
		parent::__construct(
			'movie_widget', // Base ID.
			__( 'Movie Widget', 'movie-library' ), // Name.
			array( 'description' => __( 'A Movie Widget', 'movie-library' ) ),
		);
	}
	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$tax_query_args = array();
		if ( ! empty( $instance['genre-taxonomy'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_GENRE,
				'field'    => 'term_id',
				'terms'    => $instance['genre-taxonomy'],
			);
		}
		if ( ! empty( $instance['label-taxonomy'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_LABEL,
				'field'    => 'term_id',
				'terms'    => $instance['label-taxonomy'],
			);
		}
		if ( ! empty( $instance['language-taxonomy'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => Rt_Movie::RT_MOVIE_LANGUAGE,
				'field'    => 'term_id',
				'terms'    => $instance['language-taxonomy'],
			);
		}
		if ( ! empty( $instance['director-taxonomy'] ) ) {
			$tax_query_args[] = array(
				'taxonomy' => '_rt-movie-person',
				'field'    => 'slug',
				'terms'    => "person-{$instance['director-taxonomy']}",
			);
		}
		$movie_query_args    = array(
			'post_type'      => Rt_Movie::RT_MOVIE_SLUG,
			'posts_per_page' => ! empty( $instance['movie-count'] ) ? absint( $instance['movie-count'] ) : '',
			's'              => ! empty( $instance['movie-title'] ) ? $instance['movie-title'] : '',
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					$tax_query_args,
				),
			),
		);
		$result_movies_query = new WP_Query( $movie_query_args );
		$result_movies       = $result_movies_query->posts;
		//phpcs:ignore
		echo $args['before_widget'];
		?>
		<div class="movie-main-wrapper">
			<h3 class="movie-wrapper-header">
				<?php esc_html_e( 'Movies', 'movie-library' ); ?>
			</h3>
			<div class="movie-widget-wrapper">
			<?php foreach ( $result_movies as $movie ) : ?>
				<?php

				$movie_directors  = get_movie_post_meta( $movie->ID, 'rt-movie-meta-crew-director', true );
				$movie_basic_meta = get_movie_post_meta( $movie->ID, 'rt-movie-meta-basic', true );

				$director_names = array();
				foreach ( $movie_directors as $director_id ) {
					$name             = get_post( $director_id );
					$director_names[] = $name->post_title;
				}

				?>
				<div class="widget-movie-container">
					<div class="widget-movie-poster">
						<?php echo get_the_post_thumbnail( $movie->ID ); ?>
					</div>
					<div class="widget-movie-details">
						<a href="<?php echo esc_url( get_post_permalink( $movie->ID ) ); ?>">
							<h2 class="widget-movie-title">
								<?php echo esc_html( $movie->post_title ); ?>
							</h2>
						</a>
						<p class="widget-movie-release-date">
							<?php esc_html_e( 'Release Date:', 'movie-library' ); ?>
							<?php echo esc_html( $movie_basic_meta['rt-movie-meta-basic-release-date'] ); ?>
						</p>
						<p class="widget-movie-director">
							<?php esc_html_e( 'Director:', 'movie-library' ); ?>
							<?php echo esc_html( implode( ', ', $director_names ) ); ?>
						</p>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<?php
		//phpcs:ignore
		echo $args['before_widget'];
	}
	/**
	 * Output the settings update form.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function form( $instance ) {

		$movie_labels    = get_terms( array( 'rt-movie-label' ) );
		$movie_genres    = get_terms( array( 'rt-movie-genre' ) );
		$movie_languages = get_terms( array( 'rt-movie-language' ) );

		$movie_director_args  = array(
			'post_type' => Rt_Person::RT_PERSON_SLUG,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query' => array(
				array(
					'taxonomy' => 'rt-person-career',
					'field'    => 'slug',
					'terms'    => 'director',
				),
			),
		);
		$movie_director_query = new WP_Query( $movie_director_args );
		$movie_directors      = $movie_director_query->posts;

		$form_movie_title    = ! empty( $instance['movie-title'] ) ? $instance['movie-title'] : '';
		$form_movie_count    = ! empty( $instance['movie-count'] ) ? $instance['movie-count'] : '';
		$form_movie_director = ! empty( $instance['director-taxonomy'] ) ? $instance['director-taxonomy'] : '';
		$form_movie_genre    = ! empty( $instance['genre-taxonomy'] ) ? $instance['genre-taxonomy'] : '';
		$form_movie_label    = ! empty( $instance['label-taxonomy'] ) ? $instance['label-taxonomy'] : '';
		$form_movie_language = ! empty( $instance['language-taxonomy'] ) ? $instance['language-taxonomy'] : '';
		?>
		<h3>
			<?php esc_html_e( 'Filter Movies', 'movie-library-theme' ); ?>
		</h3>
		<label for="<?php echo esc_attr( $this->get_field_id( 'movie-title' ) ); ?>">
			<?php esc_html_e( 'Movie Title', 'movie-library-theme' ); ?>
		</label>
		<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'movie-title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'movie-title' ) ); ?>" value="<?php echo esc_attr( $form_movie_title ); ?>">


		<label for="<?php echo esc_attr( $this->get_field_id( 'movie-count' ) ); ?>">
			<?php esc_html_e( 'Movie Count', 'movie-library-theme' ); ?>
		</label>
		<input type="number" name="<?php echo esc_attr( $this->get_field_name( 'movie-count' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'movie-count' ) ); ?>" value="<?php echo esc_attr( $form_movie_count ); ?>">


		<label for="<?php echo esc_attr( $this->get_field_id( 'director-taxonomy' ) ); ?>">
			<?php esc_html_e( 'Movie Director', 'movie-library-theme' ); ?>
		</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'director-taxonomy' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'director-taxonomy' ) ); ?>">
			<option selected value="">
				<?php esc_html_e( 'Select an option', 'movie-library' ); ?>
			</option>
			<?php foreach ( $movie_directors as $director ) : ?>
				<option value="<?php echo esc_attr( $director->ID ); ?>" <?php selected( (int) $form_movie_director === (int) $director->ID, true ); ?>>
					<?php echo esc_html( $director->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>


		<label for="<?php echo esc_attr( $this->get_field_id( 'genre-taxonomy' ) ); ?>">
			<?php esc_html_e( 'Movie Genre', 'movie-library-theme' ); ?>
		</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'genre-taxonomy' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'genre-taxonomy' ) ); ?>">
			<option selected value="">
				<?php esc_html_e( 'Select an option', 'movie-library' ); ?>
			</option>
			<?php foreach ( $movie_genres as $genre ) : ?>
				<option value="<?php echo esc_attr( $genre->term_id ); ?>" <?php selected( (int) $form_movie_genre === (int) $genre->term_id, true ); ?>>
					<?php echo esc_html( $genre->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>


		<label for="<?php echo esc_attr( $this->get_field_id( 'label-taxonomy' ) ); ?>">
			<?php esc_html_e( 'Movie Label', 'movie-library-theme' ); ?>
		</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'label-taxonomy' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'label-taxonomy' ) ); ?>">
			<option selected value="">
				<?php esc_html_e( 'Select an option', 'movie-library' ); ?>
			</option>
			<?php foreach ( $movie_labels as $label ) : ?>
				<option value="<?php echo esc_attr( $label->term_id ); ?>" <?php selected( (int) $form_movie_label === (int) $label->term_id, true ); ?>>
					<?php echo esc_html( $label->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>


		<label for="<?php echo esc_attr( $this->get_field_id( 'language-taxonomy' ) ); ?>">
			<?php esc_html_e( 'Movie Language', 'movie-library-theme' ); ?>
		</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'language-taxonomy' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'language-taxonomy' ) ); ?>">
			<option selected value="">
				<?php esc_html_e( 'Select an option', 'movie-library' ); ?>
			</option>
			<?php foreach ( $movie_languages as $language ) : ?>
				<option value="<?php echo esc_attr( $language->term_id ); ?>" <?php selected( (int) $form_movie_language === (int) $language->term_id, true ); ?>>
					<?php echo esc_html( $language->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php
	}
	/**
	 * Update a particular instance of a widget.
	 *
	 * @param array $new_instance The new updated settings for the particular instance of the widget.
	 * @param array $old_instance The old settings for the particular instance of the widget.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance_keys = array(
			'movie-title',
			'movie-count',
			'director-taxonomy',
			'genre-taxonomy',
			'label-taxonomy',
			'language-taxonomy',
		);

		foreach ( $instance_keys as $instance_key ) {
			$instance[ $instance_key ] = isset( $new_instance[ $instance_key ] ) ? sanitize_text_field( $new_instance[ $instance_key ] ) : '';
		}

		return $instance;

	}
}
