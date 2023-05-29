<?php
/**
 * Perform the actions in this file on uninstall plugin action.
 *
 * @package movie-library
 */

use Includes\Class_Custom_Table\Custom_Table;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}
/**
 * Remove the custom tables wp_moviemeta and wp_personmeta from the database using
 * query() method of wpdb.
 *
 * @return void
 */
function ml_delete_custom_table() {

	global $wpdb;

	$post_types = array( 'person', 'movie' );

	foreach ( $post_types as $cpt_post_type ) {

		$table = $wpdb->prefix . $cpt_post_type . 'meta';

		$sql = "DROP TABLE IF EXISTS $table";

		// Ignore prepared statement since no input from user is taken here.
		// phpcs:ignore
		$wpdb->query( $sql );
	}
}
// Ignoring this command for safety.
//phpcs:ignore
//ml_delete_custom_table();
