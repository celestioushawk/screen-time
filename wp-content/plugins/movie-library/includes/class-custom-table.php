<?php
/**
 * Class for creating the custom database table for movie post meta and person post meta.
 *
 * @package movie-library
 */

namespace Includes\Class_Custom_Table;

/**
 * Class to implement custom table for moviemeta and personmeta to save meta data about rt-movie
 * and rt-person post types. Provide wrapper functions to add, update and delete data from the table.
 */
class Custom_Table {
	/**
	 * Function to create custom database table for moviemeta and personmeta to store meta
	 * data of people and movies.
	 *
	 * @return void
	 */
	public function ml_create_custom_table() {

		global $wpdb;

		$post_types = array( 'movie', 'person' );

		foreach ( $post_types as $post_type ) {

			// SQL query for creating the table.
			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}{$post_type}meta` (
				`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`{$post_type}_id` bigint(20) unsigned NOT NULL DEFAULT '0',
				`meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
				`meta_value` longtext COLLATE utf8mb4_unicode_ci,  PRIMARY KEY (`meta_id`),
				KEY `{$post_type}_id` (`{$post_type}_id`),  KEY `meta_key` (`meta_key`(191)))
				AUTO_INCREMENT=4 DEFAULT CHARSET={$wpdb->charset} COLLATE={$wpdb->collate}";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Execute the query.
			dbDelta( $sql );

		}

	}

}
