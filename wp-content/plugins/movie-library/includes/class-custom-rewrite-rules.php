<?php
/**
 * Implement class and its functions to create custom rewrite rules for movie and person post type.
 *
 * @package movie-library
 */

namespace Includes\Class_Custom_Rewrite_Rules;

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;
use WP_Rewrite;

/**
 * Class to define and add custom rewrite rules for movie and person post type URL structure.
 */
class Custom_Rewrite_Rules {
	/**
	 * Constructor function to hook functions to their required hooks.
	 */
	public function __construct() {

		// Hook the function to init hook to fetch rewrite rules after the custom post types are registered.
		add_action( 'init', array( $this, 'ml_movie_custom_permalink_structure' ), 11 );
		add_action( 'init', array( $this, 'ml_person_custom_permalink_structure' ), 11 );

		// Hook function to post_type_link filter to change the permalink structure.
		add_action( 'post_type_link', array( $this, 'ml_movie_post_type_link' ), 10, 2 );
		add_action( 'post_type_link', array( $this, 'ml_person_post_type_link' ), 10, 2 );

	}

	/**
	 * Fetch the current permalink structure of the rt-movie post type and make changes to the structure.
	 *
	 * @return void
	 */
	public function ml_movie_custom_permalink_structure() {

		remove_permastruct( Rt_Movie::RT_MOVIE_SLUG );

		// Add our own custom permastruct.
		add_permastruct( Rt_Movie::RT_MOVIE_SLUG, '/movie/%genre%/%movie%-%post_id%' );
	}

	/**
	 * Fetch the current permalink structure of the rt-person post type and make changes to the structure.
	 *
	 * @return void
	 */
	public function ml_person_custom_permalink_structure() {

		remove_permastruct( Rt_Person::RT_PERSON_SLUG );

		// Add our own custom permastruct.
		add_permastruct( Rt_Person::RT_PERSON_SLUG, '/person/%career%/%person%-%post_id%' );
	}

	/**
	 * Create a new permalink structure for rt-movie post type and replace the placeholder values
	 * with actual values of the post and its data.
	 *
	 * @param string $permalink The permalink to modify.
	 * @param mixed  $post The current post object.
	 * @return string Return the new $permalink structure.
	 */
	public function ml_movie_post_type_link( $permalink, $post ) {

		if ( Rt_Movie::RT_MOVIE_SLUG !== $post->post_type ) {

			return $permalink;

		}

		$genres = wp_get_object_terms( $post->ID, 'rt-movie-genre', true );

		// Sort the terms in ascending and use the first term in permalink.
		$genres = wp_list_sort(
			$genres,
			array(
				'term_id' => 'ASC',
			),
		);

		if ( ! is_wp_error( $genres ) && ! empty( $genres ) && is_object( $genres[0] ) ) {

			$genre_term_slug = $genres[0]->slug;

		} else {

			$genre_term_slug = 'genre';

		}

		// Replace the placeholder with actual values using str_replace.
		$permalink = str_replace(
			array( '%genre%', '%movie%', '%post_id%' ),
			array( $genre_term_slug, $post->post_name, $post->ID ),
			$permalink
		);
		return $permalink;
	}

	/**
	 * Create a new permalink structure for rt-person post type and replace the placeholder values
	 * with actual values of the post and its data.
	 *
	 * @param string $permalink The permalink to modify.
	 * @param mixed  $post The current post object.
	 * @return string Return the new $permalink structure.
	 */
	public function ml_person_post_type_link( $permalink, $post ) {

		if ( Rt_Person::RT_PERSON_SLUG !== $post->post_type ) {

			return $permalink;

		}

		$genres = wp_get_object_terms( $post->ID, 'rt-person-career' );

		// Sort the terms in ascending and use the first term in permalink.
		$genres = wp_list_sort(
			$genres,
			array(
				'term_id' => 'ASC',
			),
		);

		if ( ! is_wp_error( $genres ) && ! empty( $genres ) && is_object( $genres[0] ) ) {

			$genre_term_slug = $genres[0]->slug;

		} else {

			$genre_term_slug = 'career';

		}

		// Replace the placeholder with actual values using str_replace.
		$permalink = str_replace(
			array( '%career%', '%person%', '%post_id%' ),
			array( $genre_term_slug, $post->post_name, $post->ID ),
			$permalink
		);
		return $permalink;
	}

	/**
	 * Function to define and add custom rewrite rules for movie post type using add_rewrite_rule().
	 *
	 * @return void
	 */
	public function create_movie_rewrite_rules() {
		add_rewrite_rule(
			'^movie/([^/]+)/([^/]+)-([0-9]+)/?$',
			'index.php?post_type=' . Rt_Movie::RT_MOVIE_SLUG . '&genre=$matches[1]&movie=$matches[2]&p=$matches[3]',
			'top'
		);
	}

	/**
	 * Function to define and add custom rewrite rules for person post type using add_rewrite_rule().
	 *
	 * @return void
	 */
	public function create_person_rewrite_rules() {
		add_rewrite_rule(
			'^person/([^/]+)/([^/]+)-([0-9]+)/?$',
			'index.php?post_type=' . Rt_Person::RT_PERSON_SLUG . '&career=$matches[1]&person=$matches[2]&p=$matches[3]',
			'top'
		);
	}
	/**
	 * Wrapper function to add custom rewrite rules.
	 *
	 * @return void
	 */
	public function add_custom_rewrite_rules() {
		$this->create_movie_rewrite_rules();
		$this->create_person_rewrite_rules();
	}

}
