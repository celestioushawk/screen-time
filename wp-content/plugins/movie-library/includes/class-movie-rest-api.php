<?php
/**
 * Create a custom REST API to get all rt-movie posts, post by ID and add, update and delete rt-movie post.
 *
 * @package movie-library
 */

namespace Includes\Class_Movie_Rest_Api;

use Includes\Class_Rt_Movie\Rt_Movie;
use WP_Error;
use WP_Query;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class to access, update and delete custom post type rt-movie.
 */
class Movie_Rest_Api {

	public const NAMESPACE = 'movie-library/v1';
	/**
	 * Hook all the functions to their necessary hooks.
	 */
	public function __construct() {

		add_action( 'rest_api_init', array( $this, 'ml_register_movie_rest_routes' ) );

	}
	/**
	 * Register all the routes for the rt-movie posts.
	 *
	 * @return void
	 */
	public function ml_register_movie_rest_routes() {

		register_rest_route(
			self::NAMESPACE,
			'/movies',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'ml_get_movies_callback' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'page'     => array(
							'description'       => __( 'Current page of the collection.', 'movie-library' ),
							'type'              => 'integer',
							'validate_callback' => array( $this, 'ml_validate_int' ),
							'sanitize_callback' => 'absint',
							'minimum'           => 1,
							'default'           => 1,
						),
						'per_page' => array(
							'description'       => __( 'Maximum number of items to be returned in result set.', 'movie-library' ),
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
							'validate_callback' => array( $this, 'ml_validate_int' ),
							'minimum'           => 1,
							'default'           => 5,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'ml_create_new_movie' ),
					'permission_callback' => array( $this, 'ml_create_movie_user_permission' ),
					'args'                => $this->ml_get_movies_query_schema(),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/movies/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'ml_get_movie_by_id_callback' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'ml_update_movie' ),
					'permission_callback' => array( $this, 'ml_create_movie_user_permission' ),
					'args'                => $this->ml_get_movies_query_schema(),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'ml_rest_delete_movie' ),
					'permission_callback' => array( $this, 'ml_delete_movie_user_permission' ),
				),
			)
		);

	}
	/**
	 * Define query schema for the GET endpoint.
	 *
	 * @return array The schema.
	 */
	public function ml_get_movies_query_schema() {

		return array(
			'ID'             => array(
				'description'       => __( 'ID of the post', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'minimum'           => 1,
			),
			'post_type'      => array(
				'description'       => __( 'Post type of the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'sanitize_text_field',
			),
			'title'          => array(
				'description'       => __( 'Title of the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'sanitize_text_field',
			),
			'author'         => array(
				'description'       => __( 'Author ID', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'date_published' => array(
				'description'       => __( 'Date of post published', 'movie-library' ),
				'type'              => 'string',
				'format'            => 'date-time',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'status'         => array(
				'description'       => __( 'Status of the post', 'movie-library' ),
				'type'              => 'string',
				'enum'              => array(
					'publish',
					'draft',
					'pending',
					'trash',
				),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'content'        => array(
				'description'       => __( 'Content of the post', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'wp_kses_post',
			),
			'excerpt'        => array(
				'description'       => __( 'Excerpt of the post', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'comments'       => array(
				'description'       => __( 'Number of comments on the post', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'thumbnail'      => array(
				'description'       => __( 'Thumbnail URL of the post', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'taxonomies'     => array(
				'description' => __( 'Taxonomies related to the post', 'movie-library' ),
				'type'        => 'object',
				'properties'  => array(
					Rt_Movie::RT_MOVIE_GENRE              => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related movie genre taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Rt_Movie::RT_MOVIE_LABEL              => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related movie label taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Rt_Movie::RT_MOVIE_LANGUAGE           => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related movie language taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Rt_Movie::RT_MOVIE_PRODUCTION_COMPANY => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related production company taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Rt_Movie::RT_MOVIE_TAG                => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related movie tag taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'_rt-movie-person'                    => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related genre taxonomy for movies', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
				),
			),
			'post_meta'      => array(
				'description' => __( 'Meta data related to the post', 'movie-library' ),
				'type'        => 'object',
				'properties'  => array(
					'rt-movie-age-rating'         => array(
						'type'        => 'string',
						'default'     => __( 'PG-13', 'movie-library' ),
						'description' => __( 'The age rating of the movie.', 'movie-library' ),
					),
					'rt-movie-meta-crew-director' => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The directors of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-actor'    => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The actors of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-writer'   => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The writers of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-producer' => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The producers of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-media-meta-images'        => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The images of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-media-meta-videos'        => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The videos of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-character'          => array(
						'description'       => __( 'Meta data related to the post', 'movie-library' ),
						'type'              => 'object',
						'patternProperties' => array(
							'^\\d+$' => array(
								'type' => 'string',
							),
						),
					),
					'rt-movie-meta-basic'         => array(
						'description' => __( 'Meta basic data related to the post', 'movie-library' ),
						'type'        => 'object',
						'properties'  => array(
							'rt-movie-meta-basic-rating'  => array(
								'type'        => 'string',
								'default'     => array(),
								'description' => __( 'The rating of the movie.', 'movie-library' ),
								'pattern'     => '^([0-9]|10)(\.[0-9])?$',
							),
							'rt-movie-meta-basic-runtime' => array(
								'type'        => 'string',
								'default'     => array(),
								'description' => __( 'The runtime of the movie.', 'movie-library' ),
							),
							'rt-movie-meta-basic-release-date' => array(
								'type'        => 'string',
								'default'     => array(),
								'description' => __( 'The release date of the movie.', 'movie-library' ),
								'pattern'     => '^\d{4}-\d{2}-\d{2}$',
							),
						),
					),

				),
			),
		);
	}

	/**
	 * Return a set of movie posts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function ml_get_movies_callback( $request ) {

		$args = array(
			'post_type'      => Rt_Movie::RT_MOVIE_SLUG,
			'posts_per_page' => $request['per_page'],
			'paged'          => $request['page'],
		);

		$query = new WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return new WP_Error(
				'no_posts_found',
				__( 'No posts found!', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		$total_found_posts = $query->found_posts;
		$max_pages         = $query->max_num_pages;

		$movie_posts = $query->posts;

		foreach ( $movie_posts as $custom_post ) {

			$result[] = $this->ml_get_movie_data( $custom_post );
		}

		$response = new WP_REST_Response( $result, 200 );

		$response->header( 'X-WP-Total', $total_found_posts );
		$response->header( 'X-WP-TotalPages', $max_pages );

		return rest_ensure_response( $response );
	}
	/**
	 * Fetch related taxonomies for a movie.
	 *
	 * @param \WP_Post $post_type current post object.
	 * @return array
	 */
	public function ml_fetch_movie_taxonomies( $post_type ) {

		$movie_terms = get_object_taxonomies( $post_type );

		if ( $movie_terms ) {
			foreach ( $movie_terms as $movie_term ) {

				$movie_term_list = get_the_terms( $post_type, $movie_term );

				$term_ids = array();
				foreach ( $movie_term_list as $movie_term_item ) {
					$term_ids[] = $movie_term_item->term_id;
				}

				$movie_term_items[ $movie_term ] = $term_ids;

			}

			return $movie_term_items;

		}
		return array();
	}
	/**
	 * Fetch related meta data for a movie.
	 *
	 * @param int $id ID of post.
	 * @return array
	 */
	public function ml_fetch_post_movie_metadata( $id ) {

		$movie_post_meta_keys = array(
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

		foreach ( $movie_post_meta_keys as $meta_key ) {
			$meta_data[ $meta_key ] = get_post_meta( $id, $meta_key, true );
		}

		return $meta_data;
	}

	/**
	 * Fetch a movie by ID passed in the parameter.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return array|\WP_Error
	 */
	public function ml_get_movie_by_id_callback( $request ) {

		$result_post = get_post( $request['id'] );

		return $this->ml_get_movie_data( $result_post );

	}

	/**
	 * Function to fetch all the details regarding a movie post type. Fetch its taxonomies, meta data
	 * and post information.
	 *
	 * @param \WP_Post $custom_post The post ID.
	 * @return array|\WP_Error Return the movie data array or WP_Error.
	 */
	public function ml_get_movie_data( $custom_post ) {

		$single_post = array(
			'ID'             => $custom_post->ID,
			'title'          => $custom_post->post_title,
			'author'         => $custom_post->post_author,
			'date_published' => $custom_post->post_date,
			'status'         => $custom_post->post_status,
			'content'        => $custom_post->post_content,
			'excerpt'        => $custom_post->post_excerpt,
			'comments'       => $custom_post->comment_count,
			'thumbnail'      => get_the_post_thumbnail_url( $custom_post->ID ),
			'taxonomies'     => $this->ml_fetch_movie_taxonomies( $custom_post ),
			'post_meta'      => $this->ml_fetch_post_movie_metadata( $custom_post->ID ),
		);

		return $single_post;
	}

	/**
	 * Function to create a new rt-movie post thorugh REST API.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param int              $update_id Optional update post id parameter.
	 * @return \WP_REST_Response|\WP_Error Return post data on success and error on failure.
	 */
	public function ml_create_new_movie( $request, $update_id = '' ) {

		$params = $request->get_params();

		$body_params = $request->get_json_params();

		if ( empty( $body_params ) ) {
			return new WP_Error(
				'rest_post_data_not_provided',
				__( 'No post data passed!', 'movie-library' ),
				array( 'status' => 400 ),
			);
		}

		if ( Rt_Movie::RT_MOVIE_SLUG !== $params['post_type'] ) {
			$params['post_type'] = Rt_Movie::RT_MOVIE_SLUG;
		}

		$post_array = array(
			'ID'           => $update_id,
			'post_title'   => $params['title'],
			'post_author'  => $params['author'],
			'post_status'  => $params['status'],
			'post_content' => $params['content'],
			'post_excerpt' => $params['excerpt'],
			'post_type'    => $params['post_type'],
			'tax_input'    => array(
				Rt_Movie::RT_MOVIE_GENRE              => $params['taxonomies'][ Rt_Movie::RT_MOVIE_GENRE ],
				Rt_Movie::RT_MOVIE_LABEL              => $params['taxonomies'][ Rt_Movie::RT_MOVIE_LABEL ],
				Rt_Movie::RT_MOVIE_LANGUAGE           => $params['taxonomies'][ Rt_Movie::RT_MOVIE_LANGUAGE ],
				Rt_Movie::RT_MOVIE_TAG                => $params['taxonomies'][ Rt_Movie::RT_MOVIE_TAG ],
				Rt_Movie::RT_MOVIE_PRODUCTION_COMPANY => $params['taxonomies'][ Rt_Movie::RT_MOVIE_PRODUCTION_COMPANY ],
			),
			'meta_input'   => array(
				'rt-movie-meta-basic'         => $params['post_meta']['rt-movie-meta-basic'],
				'rt-movie-age-rating'         => $params['post_meta']['rt-movie-age-rating'],
				'rt-movie-meta-crew-director' => $params['post_meta']['rt-movie-meta-crew-director'],
				'rt-movie-meta-crew-actor'    => $params['post_meta']['rt-movie-meta-crew-actor'],
				'rt-movie-meta-crew-writer'   => $params['post_meta']['rt-movie-meta-crew-writer'],
				'rt-movie-meta-crew-producer' => $params['post_meta']['rt-movie-meta-crew-producer'],
				'rt-media-meta-images'        => $params['post_meta']['rt-media-meta-images'],
				'rt-media-meta-videos'        => $params['post_meta']['rt-media-meta-videos'],
				'rt-movie-character'          => $params['post_meta']['rt-movie-character'],
			),
		);

		$movie_post_meta_keys = array(
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

		$new_post_id = wp_insert_post( $post_array );

		foreach ( $movie_post_meta_keys as $meta_key ) {
			update_post_meta( $new_post_id, $meta_key, $post_array['meta_input'][ $meta_key ] );
		}

		if ( ! $new_post_id instanceof WP_Error ) {
			// Set shadow taxonomy for actors assigned insdie meta-input.
			$this->ml_rest_set_shadow_taxonomy( $new_post_id );
			// Return data of the new post created.
			if ( $update_id ) {
				return new WP_REST_Response(
					__( 'Post Updated!', 'movie-library' ),
					200
				);
			}
			return new WP_REST_Response(
				__( 'Post Added!', 'movie-library' ),
				200
			);

		}

		return new WP_Error(
			'rest_post_not_inserted',
			__( 'Sorry! The post was not added!', 'movie-library' ),
			array( 'status' => 500 ),
		);

	}

	/**
	 * Function to update an existing rt-movie post thorugh REST API.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error Return updated post data on success and error on failure.
	 */
	public function ml_update_movie( $request ) {

		$query_params = $request->get_params();

		$body_params = $request->get_json_params();

		if ( empty( $body_params ) ) {
			return new WP_Error(
				'rest_post_data_not_provided',
				__( 'No post data passed!', 'movie-library' ),
				array( 'status' => 400 ),
			);
		}

		$body_params['ID'] = $query_params['id'];

		$check_post = get_post( $body_params['ID'] );

		if ( $check_post ) {

			$updated_post_id = $this->ml_create_new_movie( $request, $body_params['ID'] );

			if ( $updated_post_id ) {
				return new WP_REST_Response( __( 'Post updated!', 'movie-library' ), 200 );
			}
		}
		return new WP_Error(
			'rest_post_not_exists',
			__( 'Sorry, This post does not exist', 'movie-library' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Function to delete a rt-movie post thorugh REST API.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error Return deleted post data on success and error on failure.
	 */
	public function ml_rest_delete_movie( $request ) {

		$params = $request->get_params();

		$check_post = get_post( $params['id'] );

		if ( $check_post ) {

			wp_delete_object_term_relationships( $check_post->ID, '_rt-movie-person' );

			$movie_post_meta_keys = array(
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

			foreach ( $movie_post_meta_keys as $meta_key ) {
				delete_post_meta( $check_post->ID, $meta_key );
			}

			$deleted = wp_delete_post( $params['id'] );

			if ( $deleted ) {
				return new WP_REST_Response(
					__( 'Post Deleted Successfully!', 'movie-library' ),
					200
				);
			}
		}
		return new WP_Error(
			'rest_post_not_exists',
			__( 'Sorry, This post could not be deleted!', 'movie-library' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Function to check if the user has the permission to create or edit a movie post.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return boolean|\WP_Error Return true on success and error on failure.
	 */
	public function ml_create_movie_user_permission( $request ) {

		$params = $request->get_params();

		if ( ! current_user_can( 'edit_posts', $params['ID'] ) ) {
			return new WP_Error(
				'rest_permission_denied',
				__( 'Sorry, you dont have the permission to create or edit this post.', 'movie-library' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}
	/**
	 * Function to check if a user has the capability to delete a movie post type.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return boolean|\WP_Error Return true on success and error on failure.
	 */
	public function ml_delete_movie_user_permission( $request ) {

		$params = $request->get_query_params();

		if ( ! current_user_can( 'delete_posts', $params['id'] ) ) {
			return new WP_Error(
				'rest_permission_denied',
				__( 'Sorry, you dont have the permission to delete this post.', 'movie-library' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Create and set shadow taxonomies while creating new movies through REST API.
	 *
	 * @param int $post_id The global post object.
	 * @return void
	 */
	public function ml_rest_set_shadow_taxonomy( $post_id ) {

		$actors = get_post_meta( $post_id, 'rt-movie-meta-crew-actor', true );

		$utility_terms = array();

		foreach ( $actors as $actor_id ) {
			$utility_terms[] = sprintf( 'person-%d', $actor_id );
		}

		wp_set_object_terms( $post_id, $utility_terms, '_rt-movie-person', false );
	}
	/**
	 * Custom validate function.
	 *
	 * @param int $value Value of the paramter.
	 * @return bool
	 */
	public function ml_validate_int( $value ) {
		return is_numeric( $value );
	}
}
