<?php
/**
 * Create movie-manager role which has access to manage movie and person posts and its custom taxonomies.
 *
 * @package movie-library
 */

namespace Includes\Class_Movie_Manager;

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;
use WP_Post_Type;
use WP_Taxonomy;

/**
 * Class to implement movie-manager custom role for a user to manage movie and person posts.
 */
class Movie_Manager {
	// Constants for movie manager and admin for re-use.
	public const MOVIE_MANAGER_SLUG = 'movie-manager';
	public const ADMINISTRATOR_SLUG = 'administrator';

	/**
	 * Constructor to initialize the ml_add_roles function.
	 */
	public function __construct() {
		// Add roles on initialization of the class.
		$this->ml_add_roles();
	}

	/**
	 * Function to add the movie manager role and assign those functionalities to administrator.
	 *
	 * @return void
	 */
	public function ml_add_roles() {

		$capabilities = $this->ml_get_capabilities();

		$admin_role = get_role( self::ADMINISTRATOR_SLUG );

		foreach ( $capabilities as $capability ) {
			$admin_role->add_cap( $capability );
		}

		$capabilities = array_fill_keys( $capabilities, true );

		$capabilities['upload_files'] = true;

		remove_role( self::MOVIE_MANAGER_SLUG );

		add_role( self::MOVIE_MANAGER_SLUG, __( 'Movie Manager', 'movie-library' ), $capabilities );

	}
	/**
	 * Function to add the capabilties for custom post types and taxonomies.
	 *
	 * @return array
	 */
	public function ml_get_capabilities() {

		$capabilites = array();

		$post_types = array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG );

		// Loop for interating over capabilites for both movie and person post types.
		foreach ( $post_types as $post_type ) {

			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object instanceof WP_Post_Type ) {
				continue;
			}

			$post_type_caps = (array) $post_type_object->cap;

			unset(
				$post_type_caps['edit_post'],
				$post_type_caps['delete_post'],
				$post_type_caps['read_post'],
				$post_type_caps['create_posts'],
			);

			$capabilites = array_merge( $capabilites, array_values( $post_type_caps ) );
		}

		$taxonomies = array( Rt_Movie::RT_MOVIE_GENRE, Rt_Movie::RT_MOVIE_TAG, Rt_Movie::RT_MOVIE_LABEL, Rt_Movie::RT_MOVIE_LANGUAGE, Rt_Person::RT_PERSON_CAREER );

		foreach ( $taxonomies as $taxonomy ) {

			$taxonomy_object = get_taxonomy( $taxonomy );

			if ( ! $taxonomy_object instanceof WP_Taxonomy ) {
				continue;
			}

			$taxonomy_capabilities = (array) $taxonomy_object->cap;

			$capabilites = array_merge( $capabilites, array_values( $taxonomy_capabilities ) );
		}

		return array_unique( $capabilites );
	}

	/**
	 * Function to implement removal of role and capabilities.
	 *
	 * @return void
	 */
	public function ml_remove_roles() {

		$capabilites = $this->ml_get_capabilities();

		$admin_role = get_role( self::ADMINISTRATOR_SLUG );

		foreach ( $capabilites as $capability ) {
			$admin_role->remove_cap( $capability );
		}

		remove_role( self::MOVIE_MANAGER_SLUG );
	}
}
