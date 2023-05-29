<?php
/**
 * Create and register movie and person shortocode.
 *
 * @package movie-library
 */

namespace Includes\Class_Shortcode;

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;
use WP_Query;
/**
 * Create and register the movie and person shortcodes.
 */
class Shortcode {
	/**
	 * Link the callback functions to the hooks
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_shortcode' ] );
	}

	/**
	 * Register the shortcodes
	 *
	 * @return void
	 */
	public function register_shortcode() {
		add_shortcode( 'movie', [ $this, 'get_movies' ] );
		add_shortcode( 'person', [ $this, 'get_people' ] );
	}

	/**
	 * Get list of movies based on the filters provided in the movie shortcode
	 *
	 * @param array $atts Array of attributes.
	 * @return string
	 */
	public function get_movies( $atts = array() ) {
		$atts                    = shortcode_atts(
			[
				'person'   => '',
				'genre'    => '',
				'label'    => '',
				'language' => '',
			],
			$atts
		);
		$person_tax_query_args   = array();
		$language_tax_query_args = array();
		$label_tax_query_args    = array();
		$genre_tax_query_args    = array();
		if ( ! empty( $atts['person'] ) ) {
			$data                    = get_posts(
				[
					'post_type' => Rt_Person::RT_PERSON_SLUG,
					's'         => $atts['person'],
					'fields'    => 'ids',
				]
			);
			$slug                    = 'person-' . $data[0];
			$person_tax_query_args[] = array(
				'taxonomy' => '_rt-movie-person',
				'field'    => 'slug',
				'terms'    => $slug,
			);
		}
		if ( ! empty( $atts['language'] ) ) {
			$language_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-language',
				'field'    => 'term_id',
				'terms'    => $atts['language'],
			);
			$language_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-language',
				'field'    => 'slug',
				'terms'    => $atts['language'],
			);
			$language_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-language',
				'field'    => 'name',
				'terms'    => $atts['language'],
			);
			$language_tax_query_args['relation'] = 'OR';
		}
		if ( ! empty( $atts['genre'] ) ) {
			$genre_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-genre',
				'field'    => 'term_id',
				'terms'    => $atts['genre'],
			);
			$genre_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-genre',
				'field'    => 'slug',
				'terms'    => $atts['genre'],
			);
			$genre_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-genre',
				'field'    => 'genre',
				'terms'    => $atts['genre'],
			);
			$genre_tax_query_args['relation'] = 'OR';
		}
		if ( ! empty( $atts['label'] ) ) {
			$label_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-label',
				'field'    => 'term_id',
				'terms'    => $atts['label'],
			);
			$label_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-label',
				'field'    => 'slug',
				'terms'    => $atts['label'],
			);
			$label_tax_query_args[]           = array(
				'taxonomy' => 'rt-movie-label',
				'field'    => 'name',
				'terms'    => $atts['label'],
			);
			$label_tax_query_args['relation'] = 'OR';
		}
		$main_tax_query_args = array();
		if ( $person_tax_query_args ) {
			$main_tax_query_args[] = $person_tax_query_args;
		}
		if ( $language_tax_query_args ) {

			$main_tax_query_args[] = $language_tax_query_args;
		}
		if ( $genre_tax_query_args ) {

			$main_tax_query_args[] = $genre_tax_query_args;
		}
		if ( $label_tax_query_args ) {

			$main_tax_query_args[] = $label_tax_query_args;
		}
		$main_tax_query_args['relation'] = 'AND';
		$data                            = new WP_Query(
			[
				'post_type'              => Rt_Movie::RT_MOVIE_SLUG,
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'tax_query'              => $main_tax_query_args,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_term_meta_cache' => false,
			]
		);
		if ( 0 === $data->post_count ) {
			return esc_html__( 'No movies found! Sorry!', 'movie-library' );
		}
		$movies_from_db = array();
		foreach ( $data->posts as $movie ) {
			$movies_from_db[] = array(
				'id'   => $movie->ID,
				'name' => $movie->post_title,
			);
		}
		$content_string = '';
		foreach ( $movies_from_db as $movie_post ) {
			$content_string       .= '<ul>';
			$post_thumbnail        = get_the_post_thumbnail( $movie_post['id'] );
			$movie_basic_meta_data = get_movie_post_meta( $movie_post['id'], 'rt-movie-meta-basic', true );
			$movie_director        = get_movie_post_meta( $movie_post['id'], 'rt-movie-meta-crew-director', true );
			$movie_actor           = get_movie_post_meta( $movie_post['id'], 'rt-movie-meta-crew-actor', true );
			$content_string       .= '<h3>' . esc_html__( 'Movie', 'movie-library' ) . ': ';
			$content_string       .= $movie_post['name'] . '</h3>';
			$content_string       .= $post_thumbnail;
			$content_string       .= '<li>' . esc_html__( 'Release Date', 'movie-library' ) . ': ';
			$content_string       .= $movie_basic_meta_data['rt-movie-meta-basic-release-date'] . '</li>';
			$content_string       .= '<li>' . esc_html__( 'Director', 'movie-library' ) . ': ';
			$directors_names       = array();
			foreach ( $movie_director as $director ) {
					$director_name     = get_post( $director );
					$directors_names[] = $director_name->post_title;
			}
			$content_string .= implode( ', ', $directors_names );
			$content_string .= '</li>';
			$content_string .= '<li>' . esc_html__( 'Actor', 'movie-library' ) . ': ';
			$actors_names    = array();
			foreach ( $movie_actor as $actor ) {
					$actor_name     = get_post( $actor );
					$actors_names[] = $actor_name->post_title;
			}
			$content_string .= implode( ', ', $actors_names );
			$content_string .= '</li>';
			$content_string .= '</ul>';
		}
		return $content_string;
	}

	/**
	 * Get list of people based on the paramters provided in the person shortcode
	 *
	 * @param array $atts Array of shortcode attributes.
	 * @return string
	 */
	public function get_people( $atts = array() ) {
		$atts           = shortcode_atts(
			[
				'career' => '',
			],
			$atts
		);
		$tax_query_args = array();
		if ( ! empty( $atts['career'] ) ) {
			$tax_query_args[]           = array(
				'taxonomy' => 'rt-person-career',
				'field'    => 'term_id',
				'terms'    => $atts['career'],
			);
			$tax_query_args[]           = array(
				'taxonomy' => 'rt-person-career',
				'field'    => 'slug',
				'terms'    => $atts['career'],
			);
			$tax_query_args[]           = array(
				'taxonomy' => 'rt-person-career',
				'field'    => 'name',
				'terms'    => $atts['career'],
			);
			$tax_query_args['relation'] = 'OR';
		}
		$data           = new WP_Query(
			[
				'post_type' => 'rt-person',
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'tax_query' => $tax_query_args,
			]
		);
		$people_from_db = array();
		foreach ( $data->posts as $person ) {
			$people_from_db[] = array(
				'id'   => $person->ID,
				'name' => $person->post_title,
			);
		}
		$content_string = '';
		foreach ( $people_from_db as $person ) {
			$person_basic_meta_data  = get_person_post_meta( $person['id'], 'rt-person-meta-basic', true );
			$person_social_meta_data = get_person_post_meta( $person['id'], 'rt-person-meta-social', true );
			$content_string         .= "<h3>{$person['name']}</h3>";
			$content_string         .= '<ul><li>' . esc_html__( 'Birth Date', 'movie-library' ) . ': ';
			$content_string         .= $person_basic_meta_data['rt-person-meta-basic-birth-date'] . '</li>';
			$content_string         .= '<li>' . esc_html__( 'Birth Place', 'movie-library' ) . ': ';
			$content_string         .= $person_basic_meta_data['rt-person-meta-basic-birth-place'] . '</li></ul>';
			$content_string         .= '<h4>' . esc_html__( 'Social Links', 'movie-library' ) . '</h4>';
			$content_string         .= '<ul><li>' . esc_html__( 'Twitter', 'movie-library' ) . ': ';
			$content_string         .= $person_social_meta_data['rt-person-meta-social-twitter'] . '</li>';
			$content_string         .= '<li>' . esc_html__( 'Instagram', 'movie-library' ) . ': ';
			$content_string         .= $person_social_meta_data['rt-person-meta-social-instagram'] . '</li>';
			$content_string         .= '<li>' . esc_html__( 'Facebook', 'movie-library' ) . ': ';
			$content_string         .= $person_social_meta_data['rt-person-meta-social-facebook'] . '</li>';
			$content_string         .= '<li>' . esc_html__( 'Web', 'movie-library' ) . ': ';
			$content_string         .= $person_social_meta_data['rt-person-meta-social-web'] . '</li></ul>';
		}
		return $content_string;
	}
}
