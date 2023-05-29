<?php
/**
 * Define helper functions for custom table insertion, update and deletion operations.
 *
 * @package movie-library
 */

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;

/**
 * Wrapper function to add meta data of the movie post type to the custom database table.
 *
 * @param mixed   $post_id The post_id of the post meta data being added.
 * @param string  $meta_key The meta_key of the meta data being added.
 * @param mixed   $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @param boolean $unique Whether the specified metadata key should be unique for the object. Default false.
 * @return int|bool|void The meta ID on success, void on post revision.
 */
function add_movie_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	return add_metadata( 'movie', $post_id, $meta_key, $meta_value, $unique );

}

/**
 * Wrapper function to add meta data of the person post type to the custom database table.
 *
 * @param mixed   $post_id The post_id of the post meta data being added.
 * @param string  $meta_key The meta_key of the meta data being added.
 * @param mixed   $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @param boolean $unique Whether the specified metadata key should be unique for the object. Default false.
 * @return int|false|void The meta ID on success, false on failure.
 */
function add_person_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	return add_metadata( 'person', $post_id, $meta_key, $meta_value, $unique );
}

/**
 * Retrieve movie post meta field for the given post ID.
 *
 * @param int     $post_id Post ID.
 * @param string  $key The $meta_key for which meta data we need to retrieve.
 * @param boolean $single If true, return only the first value of the specified `$key`.
 * @return mixed  An array of values if `$single` is false.
 *                The value of the meta field if `$single` is true.
 */
function get_movie_post_meta( $post_id, $key = '', $single = false ) {

	if ( get_metadata( 'movie', $post_id, $key, $single ) ) {

		return get_metadata( 'movie', $post_id, $key, $single );

	}
	return get_metadata( 'post', $post_id, $key, $single );

}
/**
 * Retrieve person post meta field for the given post ID.
 *
 * @param int     $post_id Post ID.
 * @param string  $key The $meta_key for which meta data we need to retrieve.
 * @param boolean $single If true, return only the first value of the specified `$key`.
 * @return mixed  An array of values if `$single` is false.
 *                The value of the meta field if `$single` is true.
 */
function get_person_post_meta( $post_id, $key = '', $single = false ) {

	if ( get_metadata( 'person', $post_id, $key, $single ) ) {

		return get_metadata( 'person', $post_id, $key, $single );

	}
	return get_metadata( 'post', $post_id, $key, $single );

}
/**
 * Wrapper function to update meta data of the movie data type to the custom database table.
 *
 * @param mixed   $post_id The post_id of the post meta data being updated.
 * @param string  $meta_key The meta_key of the meta data being updated.
 * @param mixed   $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @param boolean $prev_value Previous value to check before updating. Default empty.
 * @return int|false|void The meta ID on success, false on failure.
 */
function update_movie_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( get_metadata( 'post', $post_id, $meta_key, true ) ) {

		delete_metadata( 'post', $post_id, $meta_key );

	}
	return update_metadata( 'movie', $post_id, $meta_key, $meta_value );

}
/**
 * Wrapper function to update meta data of the person post type to the custom database table.
 *
 * @param mixed   $post_id The post_id of the post meta data being updated.
 * @param string  $meta_key The meta_key of the meta data being updated.
 * @param mixed   $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @param boolean $prev_value Previous value to check before updating. Default empty.
 * @return int|false|void The meta ID on success, false on failure.
 */
function update_person_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( get_metadata( 'post', $post_id, $meta_key, true ) ) {

		delete_metadata( 'post', $post_id, $meta_key );

	}
	return update_metadata( 'person', $post_id, $meta_key, $meta_value );

}
/**
 * Wrapper function to delete meta data of the custom data type to the custom database table.
 *
 * @param mixed  $post_id The post_id of the post meta data being deleted.
 * @param string $meta_key The meta_key of the meta data being deleted.
 * @param mixed  $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @return void
 */
function delete_movie_post_meta( $post_id, $meta_key, $meta_value = '' ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	delete_metadata( 'movie', $post_id, $meta_key, $meta_value );

	delete_metadata( 'post', $post_id, $meta_key, $meta_value );
}
/**
 * Wrapper function to delete meta data of the custom data type to the custom database table.
 *
 * @param mixed  $post_id The post_id of the post meta data being deleted.
 * @param string $meta_key The meta_key of the meta data being deleted.
 * @param mixed  $meta_value The meta_value corresponding to the meta_key of the meta data.
 * @return void
 */
function delete_person_post_meta( $post_id, $meta_key, $meta_value = '' ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	delete_metadata( 'person', $post_id, $meta_key, $meta_value );

	delete_metadata( 'post', $post_id, $meta_key, $meta_value );
}
