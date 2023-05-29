<?php
/**
 * Create a custom REST API to get all rt-person posts, post by ID and add, update and delete rt-person post.
 *
 * @package movie-library
 */

namespace Includes\Class_Person_Rest_Api;

use Includes\Class_Rt_Person\Rt_Person;
use WP_Error;
use WP_Query;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class to access, update and delete custom post type rt-person.
 */
class Person_Rest_Api {

	public const NAMESPACE = 'movie-library/v1';
	/**
	 * Hook all the functions to their necessary hooks.
	 */
	public function __construct() {

		add_action( 'rest_api_init', array( $this, 'ml_register_person_rest_routes' ) );

	}
	/**
	 * Register all the routes for the rt-movie posts.
	 *
	 * @return void
	 */
	public function ml_register_person_rest_routes() {

		register_rest_route(
			self::NAMESPACE,
			'/people',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'ml_get_people_callback' ),
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
					'callback'            => array( $this, 'ml_create_new_person' ),
					'permission_callback' => array( $this, 'ml_create_person_user_permission' ),
					'args'                => $this->ml_get_person_query_schema(),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/people/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'ml_get_person_by_id_callback' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'ml_update_person' ),
					'permission_callback' => array( $this, 'ml_create_person_user_permission' ),
					'args'                => $this->ml_get_person_query_schema(),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'ml_rest_delete_person' ),
					'permission_callback' => array( $this, 'ml_delete_person_user_permission' ),
				),
			)
		);

	}

	/**
	 * Define query schema for the GET endpoint.
	 *
	 * @return array The schema.
	 */
	public function ml_get_person_query_schema() {

		return array(
			'ID'             => array(
				'description'       => __( 'ID of the person', 'movie-library' ),
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
				'description'       => __( 'Name of the person.', 'movie-library' ),
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
					Rt_Person::RT_PERSON_CAREER => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'Related person career taxonomy for person', 'movie-library' ),
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
					'rt-person-meta-basic'       => array(
						'type'        => 'object',
						'description' => __( 'The basic meta data of the person', 'movie-library' ),
						'properties'  => array(
							'rt-person-meta-basic-birth-date'  => array(
								'type'        => 'string',
								'default'     => array(),
								'description' => __( 'The birth date of the person.', 'movie-library' ),
								'pattern'     => '^\d{4}-\d{2}-\d{2}$',
							),
							'rt-person-meta-basic-birth-place' => array(
								'type'              => 'string',
								'default'           => array(),
								'description'       => __( 'The birth place of the person.', 'movie-library' ),
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
					'rt-person-meta-social'      => array(
						'type'        => 'object',
						'description' => __( 'The basic social data of the person', 'movie-library' ),
						'properties'  => array(
							'rt-person-meta-social-web' => array(
								'type'              => 'uri',
								'description'       => __( 'The website url of the person', 'movie-library' ),
								'sanitize_callback' => 'sanitize_url',
							),
							'rt-person-meta-social-facebook' => array(
								'type'              => 'uri',
								'description'       => __( 'The facebook url of the person', 'movie-library' ),
								'sanitize_callback' => 'sanitize_url',
							),
							'rt-person-meta-social-instagram' => array(
								'type'              => 'uri',
								'description'       => __( 'The instagram url of the person', 'movie-library' ),
								'sanitize_callback' => 'sanitize_url',
							),
							'rt-person-meta-social-twitter' => array(
								'type'              => 'uri',
								'description'       => __( 'The twitter url of the person', 'movie-library' ),
								'sanitize_callback' => 'sanitize_url',
							),
						),
					),
					'rt-media-meta-images'       => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The images of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-media-meta-videos'       => array(
						'type'        => 'array',
						'default'     => array(),
						'description' => __( 'The videos of the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-person-full-name'        => array(
						'type'              => 'string',
						'description'       => __( 'The full name of the person.', 'movie-library' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'rt-person-debut-year'       => array(
						'type'              => 'string',
						'description'       => __( 'The debut year of the person.', 'movie-library' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'rt-person-debut-movie'      => array(
						'type'              => 'string',
						'description'       => __( 'The debut movie of the person.', 'movie-library' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'rt-person-debut-movie-year' => array(
						'type'              => 'string',
						'description'       => __( 'The debut movie year of the person.', 'movie-library' ),
						'sanitize_callback' => 'sanitize_text_field',
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
	public function ml_get_people_callback( $request ) {

		$args = array(
			'post_type'      => Rt_Person::RT_PERSON_SLUG,
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

		$person_posts = $query->posts;

		foreach ( $person_posts as $custom_post ) {

			$result[] = $this->ml_get_person_data( $custom_post );
		}

		$response = new WP_REST_Response( $result, 200 );

		$response->header( 'X-WP-Total', $total_found_posts );
		$response->header( 'X-WP-TotalPages', $max_pages );

		return rest_ensure_response( $response );
	}

	/**
	 * Fetch related taxonomies for a person.
	 *
	 * @param \WP_Post $post_type current post object.
	 * @return array
	 */
	public function ml_fetch_person_taxonomies( $post_type ) {

		$person_terms = get_object_taxonomies( $post_type );

		if ( $person_terms ) {
			foreach ( $person_terms as $person_term ) {

				$person_term_list = get_the_terms( $post_type, $person_term );

				$term_ids = array();
				foreach ( $person_term_list as $person_term_item ) {
					$term_ids[] = $person_term_item->term_id;
				}

				$person_term_items[ $person_term ] = $term_ids;

			}

			return $person_term_items;

		}
		return array();
	}
	/**
	 * Fetch related meta data for a movie.
	 *
	 * @param int $id ID of post.
	 * @return array
	 */
	public function ml_fetch_post_person_metadata( $id ) {

		$person_post_meta_keys = array(
			'rt-person-full-name',
			'rt-person-debut-year',
			'rt-person-debut-movie',
			'rt-person-debut-movie-year',
			'rt-media-meta-images',
			'rt-media-meta-videos',
			'rt-person-meta-social',
			'rt-person-meta-basic',
		);

		foreach ( $person_post_meta_keys as $meta_key ) {
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
	public function ml_get_person_by_id_callback( $request ) {

		$result_post = get_post( $request['id'] );

		return $this->ml_get_person_data( $result_post );

	}

	/**
	 * Function to fetch all the details regarding a movie post type. Fetch its taxonomies, meta data
	 * and post information.
	 *
	 * @param \WP_Post $custom_post The post ID.
	 * @return array|\WP_Error Return the movie data array or WP_Error.
	 */
	public function ml_get_person_data( $custom_post ) {

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
			'taxonomies'     => $this->ml_fetch_person_taxonomies( $custom_post ),
			'post_meta'      => $this->ml_fetch_post_person_metadata( $custom_post->ID ),
		);

		return $single_post;
	}

	/**
	 * Function to create a new rt-person post thorugh REST API.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param int              $update_id Optional update post id parameter.
	 * @return \WP_REST_Response|\WP_Error Return post data on success and error on failure.
	 */
	public function ml_create_new_person( $request, $update_id = '' ) {

		$params = $request->get_params();

		$body_params = $request->get_json_params();

		if ( empty( $body_params ) ) {
			return new WP_Error(
				'rest_post_data_not_provided',
				__( 'No post data passed!', 'movie-library' ),
				array( 'status' => 400 ),
			);
		}

		if ( Rt_Person::RT_PERSON_SLUG !== $params['post_type'] ) {
			$params['post_type'] = Rt_Person::RT_PERSON_SLUG;
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
				Rt_Person::RT_PERSON_CAREER => $params['taxonomies'][ Rt_Person::RT_PERSON_CAREER ],
			),
			'meta_input'   => array(
				'rt-person-full-name'        => $params['post_meta']['rt-person-full-name'],
				'rt-person-debut-year'       => $params['post_meta']['rt-person-debut-year'],
				'rt-person-debut-movie'      => $params['post_meta']['rt-person-debut-movie'],
				'rt-person-debut-movie-year' => $params['post_meta']['rt-person-debut-movie-year'],
				'rt-media-meta-images'       => $params['post_meta']['rt-media-meta-images'],
				'rt-person-meta-social'      => $params['post_meta']['rt-person-meta-social'],
				'rt-person-meta-basic'       => $params['post_meta']['rt-person-meta-basic'],
			),
		);

		$new_post_id = wp_insert_post( $post_array );

		$person_post_meta_keys = array(
			'rt-person-full-name',
			'rt-person-debut-year',
			'rt-person-debut-movie',
			'rt-person-debut-movie-year',
			'rt-media-meta-images',
			'rt-media-meta-videos',
			'rt-person-meta-social',
			'rt-person-meta-basic',
		);

		foreach ( $person_post_meta_keys as $meta_key ) {
			update_post_meta( $new_post_id, $meta_key, $post_array['meta_input'][ $meta_key ] );
		}

		if ( ! $new_post_id instanceof WP_Error ) {
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
	public function ml_update_person( $request ) {

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

			$updated_post_id = $this->ml_create_new_person( $request, $body_params['ID'] );

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
	 * Function to delete a rt-person post thorugh REST API.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error Return deleted post data on success and error on failure.
	 */
	public function ml_rest_delete_person( $request ) {

		$params = $request->get_params();

		$check_post = get_post( $params['id'] );

		if ( $check_post ) {

			$person_post_meta_keys = array(
				'rt-person-full-name',
				'rt-person-debut-year',
				'rt-person-debut-movie',
				'rt-person-debut-movie-year',
				'rt-media-meta-images',
				'rt-media-meta-videos',
				'rt-person-meta-social',
				'rt-person-meta-basic',
			);

			foreach ( $person_post_meta_keys as $meta_key ) {
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
	 * Function to check if the user has the permission to create or edit a person post.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return boolean|\WP_Error Return true on success and error on failure.
	 */
	public function ml_create_person_user_permission( $request ) {

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
	 * Function to check if a user has the capability to delete a person post type.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return boolean|\WP_Error Return true on success and error on failure.
	 */
	public function ml_delete_person_user_permission( $request ) {

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
	 * Custom validate function.
	 *
	 * @param int $value Value of the paramter.
	 * @return bool
	 */
	public function ml_validate_int( $value ) {
		return is_numeric( $value );
	}
}
