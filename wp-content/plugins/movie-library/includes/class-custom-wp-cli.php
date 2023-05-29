<?php
/**
 * Define custom WP CLI commands to execute jobs.
 *
 * @package movie-library
 */

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;

/**
 * Class to implement custom WP CLI commands to export movies and people's posts
 * to a CSV file.
 */
class Custom_Wp_Cli {

	/**
	 * Exports rt-movie, rt-person post types in csv file.
	 *
	 * ## OPTIONS
	 *
	 * [<post_type>]
	 * : Post type to export.
	 *
	 * ## EXAMPLES
	 *     wp ml_export rt-movie
	 *     wp ml_export rt-person
	 *
	 * @param array $args       Positional arguments.
	 *                          $args[0] is post type.
	 */
	public function __invoke( $args ) {

		if ( empty( $args ) ) {
			WP_CLI::error( __( 'Please provide args!', 'movie-library' ) );
			return;
		}

		if ( Rt_Movie::RT_MOVIE_SLUG === $args[0] ) {

			$this->export_data( Rt_Movie::RT_MOVIE_SLUG );

		} elseif ( Rt_Person::RT_PERSON_SLUG === $args[0] ) {

			$this->export_data( Rt_Person::RT_PERSON_SLUG );

		} else {
			WP_CLI::error( __( 'Please provide a valid post type!', 'movie-library' ) );
		}

	}
	/**
	 * Export the data to a .csv file by fetching the posts from database and writing
	 * to a .csv file.
	 *
	 * @param string $post_type The post type of the post data to fetch.
	 * @return void
	 */
	public function export_data( $post_type ) {

		WP_Filesystem();

		global $wp_filesystem;

		// Default file path where the file will be created.
		$upload_dir = wp_get_upload_dir();
		$path       = $upload_dir['path'] . '/custom_export_data';

		if ( ! $wp_filesystem->is_dir( $path ) ) {
			$subdir = $wp_filesystem->mkdir( $path );
		}

		$path .= '/' . uniqid( $post_type ) . '.csv';

		// Meta keys for person meta data.
		$person_post_meta = array(
			'rt-movie-age-rating',
			'rt-movie-meta-crew-director',
			'rt-movie-meta-crew-actor',
			'rt-movie-meta-crew-writer',
			'rt-movie-meta-crew-producer',
			'rt-media-meta-images',
			'rt-media-meta-videos',
			'rt-movie-character',
			'rt-movie-meta-basic',
		);
		// Meta keys for movie meta data.
		$movie_post_meta = array(
			'rt-movie-age-rating',
			'rt-movie-meta-crew-director',
			'rt-movie-meta-crew-actor',
			'rt-movie-meta-crew-writer',
			'rt-movie-meta-crew-producer',
			'rt-media-meta-images',
			'rt-media-meta-videos',
			'rt-movie-character',
			'rt-movie-meta-basic',
		);

		$offset = 0;
		// Header row containing row titles for .csv file.
		$header_row = array(
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'pinged',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
			'filter',
		);
		// Append meta keys into header row to show post meta.
		if ( Rt_Movie::RT_MOVIE_SLUG === $post_type ) {
			foreach ( $movie_post_meta as $movie_meta_key ) {
				$header_row[] = $movie_meta_key;
			}
		} else {
			foreach ( $person_post_meta as $person_meta_key ) {
				$header_row[] = $person_meta_key;
			}
		}

		$progress = '';

		ob_start();

		$output_stream = fopen( 'php://output', 'w' );

		fputcsv( $output_stream, $header_row );

		$first_fetch = true;

		$page = 1;

		do {

			$query_args = array(
				'post_type'                   => $post_type,
				'update_post_meta_cache'      => false,
				'update_post_term_meta_cache' => false,
				'no_found_rows'               => $first_fetch,
				'posts_per_page'              => 100,
				'paged'                       => $page,
			);

			$data_query = new WP_Query( $query_args );

			// Condition to break the loop, if post_count for current query becomes 0.
			if ( 0 === $data_query->post_count ) {
				break;
			}
			// Increment the page to next.
			$page++;

			if ( $first_fetch ) {
				// translators: %s is the post type.
				$progress    = WP_CLI\Utils\make_progress_bar( sprintf( __( 'Exporting %s posts to CSV', 'movie-library' ), $post_type ), $data_query->found_posts );
				$first_fetch = false;
			}

			$data = $data_query->posts;

			foreach ( $data as $data_post ) {

				$post_data_array = (array) $data_post;

				// Fetch the meta data for each post and append to the row.
				if ( Rt_Movie::RT_MOVIE_SLUG === $post_type ) {
					$current_post_meta = get_post_meta( $data_post->ID );
					foreach ( $movie_post_meta as $movie_meta_key ) {
						$post_data_array[ $movie_meta_key ] = maybe_serialize( $current_post_meta[ $movie_meta_key ][0] );
					}
				} else {
					$current_post_meta = get_post_meta( $data_post->ID );
					foreach ( $person_post_meta as $person_meta_key ) {
						$post_data_array[ $person_meta_key ] = maybe_serialize( $current_post_meta[ $person_meta_key ][0] );
					}
				}

				fputcsv( $output_stream, $post_data_array );
				// Move forward the progress bar.
				$progress->tick();
			}
		} while ( true );
		//phpcs:ignore
		fclose( $output_stream );

		$wp_filesystem->put_contents( $path, ob_get_clean() );

		$progress->finish();
		// translators: %s is the file name.
		WP_CLI::success( sprintf( __( 'Your generated file is at: %s ', 'movie-library' ), $path ) );

	}

}

WP_CLI::add_command( 'ml_export', 'Custom_Wp_Cli' );
