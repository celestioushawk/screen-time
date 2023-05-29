<?php
/**
 * Create rt-movie custom post type and its taxonomies and register its meta boxes
 *
 * @package movie-library
 */

namespace Includes\Class_Rt_Movie;

use Includes\Class_Rt_Person\Rt_Person;
use WP_Query;

/**
 * Create rt-movie custom post type and its taxonomies and register their meta boxes.
 */
class Rt_Movie {
	public const RT_MOVIE_SLUG               = 'rt-movie';
	public const RT_MOVIE_TAG                = 'rt-movie-tag';
	public const RT_MOVIE_LABEL              = 'rt-movie-label';
	public const RT_MOVIE_LANGUAGE           = 'rt-movie-language';
	public const RT_MOVIE_GENRE              = 'rt-movie-genre';
	public const RT_MOVIE_PRODUCTION_COMPANY = 'rt-movie-production-company';
	/**
	 * Initializes the class and hook all callback functions with their hooks.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'rt_movie_init' ] );
		add_action( 'init', [ $this, 'create_genre_taxonomy' ] );
		add_action( 'init', [ $this, 'create_label_taxonomy' ] );
		add_action( 'init', [ $this, 'create_language_taxonomy' ] );
		add_action( 'init', [ $this, 'create_production_company_taxonomy' ] );
		add_action( 'init', [ $this, 'create_movie_tags_taxonomy' ] );
		add_action( 'init', [ $this, 'create_shadow_movie_person_taxonomy' ] );
		add_action( 'add_meta_boxes', [ $this, 'create_meta_boxes' ] );
		add_action( 'save_post_rt-movie', [ $this, 'save_meta_box_values' ] );
		add_action( 'save_post_rt-movie', [ $this, 'save_crew_meta_box_values' ] );
	}

	/**
	 * Create the rt-movie post type
	 *
	 * @return void
	 */
	public function rt_movie_init() {
		register_post_type(
			'rt-movie',
			[
				'labels'                => [
					'name'                  => __( 'Movies', 'movie-library' ),
					'singular_name'         => __( 'Movie', 'movie-library' ),
					'all_items'             => __( 'All Movies', 'movie-library' ),
					'archives'              => __( 'Movie Archives', 'movie-library' ),
					'attributes'            => __( 'Movie Attributes', 'movie-library' ),
					'insert_into_item'      => __( 'Insert into Movie', 'movie-library' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Movie', 'movie-library' ),
					'featured_image'        => _x( 'Movie Poster', 'rt-movie', 'movie-library' ),
					'set_featured_image'    => _x( 'Set movie poster', 'rt-movie', 'movie-library' ),
					'remove_featured_image' => _x( 'Remove movie poster', 'rt-movie', 'movie-library' ),
					'use_featured_image'    => _x( 'Use as featured image', 'rt-movie', 'movie-library' ),
					'filter_items_list'     => __( 'Filter Movies list', 'movie-library' ),
					'items_list_navigation' => __( 'Movies list navigation', 'movie-library' ),
					'items_list'            => __( 'Movies list', 'movie-library' ),
					'new_item'              => __( 'New Movie', 'movie-library' ),
					'add_new'               => __( 'Add New', 'movie-library' ),
					'add_new_item'          => __( 'Add New Movie', 'movie-library' ),
					'edit_item'             => __( 'Edit Movie', 'movie-library' ),
					'view_item'             => __( 'View Movie', 'movie-library' ),
					'view_items'            => __( 'View Movies', 'movie-library' ),
					'search_items'          => __( 'Search Movies', 'movie-library' ),
					'not_found'             => __( 'No Movies found', 'movie-library' ),
					'not_found_in_trash'    => __( 'No Movies found in trash', 'movie-library' ),
					'parent_item_colon'     => __( 'Parent Movie:', 'movie-library' ),
					'menu_name'             => __( 'Movies', 'movie-library' ),
				],
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'supports'              => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments', 'custom-fields' ],
				'has_archive'           => true,
				'query_var'             => true,
				'menu_position'         => null,
				'menu_icon'             => 'dashicons-editor-video',
				'show_in_rest'          => true,
				'rest_base'             => 'rt-movie',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'capability_type'       => 'movie',
				'map_meta_cap'          => true,
			]
		);
	}

	/**
	 * Create the genre taxonomy for rt-person post type
	 *
	 * @return void
	 */
	public function create_genre_taxonomy() {
		$labels = array(
			'name'                       => __( 'Genres', 'movie-library' ),
			'singular_name'              => __( 'Genre', 'movie-library' ),
			'search_items'               => __( 'Search Genres', 'movie-library' ),
			'all_items'                  => __( 'All Genres', 'movie-library' ),
			'edit_item'                  => __( 'Edit Genre', 'movie-library' ),
			'update_item'                => __( 'Update Genre', 'movie-library' ),
			'add_new_item'               => __( 'Add New Genre', 'movie-library' ),
			'new_item_name'              => __( 'New Genre Name', 'movie-library' ),
			'menu_name'                  => __( 'Genre', 'movie-library' ),
			'view_item'                  => __( 'View Genre', 'movie-library' ),
			'popular_items'              => __( 'Popular Genre', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Genres with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove genres', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular genres', 'movie-library' ),
			'not_found'                  => __( 'No genres found', 'movie-library' ),
		);
		register_taxonomy(
			'rt-movie-genre',
			self::RT_MOVIE_SLUG,
			array(
				'hierarchical'          => true,
				'labels'                => $labels,
				'public'                => true,
				'show_in_nav_menus'     => true,
				'show_tagcloud'         => false,
				'show_in_rest'          => true,
				'show_admin_column'     => true,
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'capabilities'          => array(
					'manage_terms' => 'manage_genres',
					'edit_terms'   => 'edit_genres',
					'delete_terms' => 'delete_genres',
					'assign_terms' => 'assign_genres',
				),
			)
		);
	}

	/**
	 * Create the label taxonomy for rt-movie post type
	 *
	 * @return void
	 */
	public function create_label_taxonomy() {
		$labels = array(
			'name'                       => __( 'Labels', 'movie-library' ),
			'singular_name'              => __( 'Label', 'movie-library' ),
			'search_items'               => __( 'Search Labels', 'movie-library' ),
			'all_items'                  => __( 'All Labels', 'movie-library' ),
			'edit_item'                  => __( 'Edit Label', 'movie-library' ),
			'update_item'                => __( 'Update Label', 'movie-library' ),
			'add_new_item'               => __( 'Add New Label', 'movie-library' ),
			'new_item_name'              => __( 'New Label Name', 'movie-library' ),
			'menu_name'                  => __( 'Label', 'movie-library' ),
			'view_item'                  => __( 'View Label', 'movie-library' ),
			'popular_items'              => __( 'Popular Label', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Labels with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove labels', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular labels', 'movie-library' ),
			'not_found'                  => __( 'No labels found', 'movie-library' ),
		);
			register_taxonomy(
				'rt-movie-label',
				self::RT_MOVIE_SLUG,
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_labels',
						'edit_terms'   => 'edit_labels',
						'delete_terms' => 'delete_labels',
						'assign_terms' => 'assign_labels',
					),
				)
			);
	}

	/**
	 * Create the language taxonomy for rt-movie post type
	 *
	 * @return void
	 */
	public function create_language_taxonomy() {
		$labels = array(
			'name'                       => __( 'Languages', 'movie-library' ),
			'singular_name'              => __( 'Language', 'movie-library' ),
			'search_items'               => __( 'Search Languages', 'movie-library' ),
			'all_items'                  => __( 'All Languages', 'movie-library' ),
			'edit_item'                  => __( 'Edit Language', 'movie-library' ),
			'update_item'                => __( 'Update Language', 'movie-library' ),
			'add_new_item'               => __( 'Add New Language', 'movie-library' ),
			'new_item_name'              => __( 'New Language Name', 'movie-library' ),
			'menu_name'                  => __( 'Language', 'movie-library' ),
			'view_item'                  => __( 'View Language', 'movie-library' ),
			'popular_items'              => __( 'Popular Language', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Languages with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove Languages', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular Languages', 'movie-library' ),
			'not_found'                  => __( 'No Languages found', 'movie-library' ),
		);
			register_taxonomy(
				'rt-movie-language',
				self::RT_MOVIE_SLUG,
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_languages',
						'edit_terms'   => 'edit_languages',
						'delete_terms' => 'delete_languages',
						'assign_terms' => 'assign_languages',
					),
				)
			);
	}

	/**
	 * Create production company for rt-movie post type.
	 *
	 * @return void
	 */
	public function create_production_company_taxonomy() {
		$labels = array(
			'name'                       => __( 'Production Companies', 'movie-library' ),
			'singular_name'              => __( 'Production Company', 'movie-library' ),
			'search_items'               => __( 'Search Production Companies', 'movie-library' ),
			'all_items'                  => __( 'All Production Companies', 'movie-library' ),
			'edit_item'                  => __( 'Edit Production Company', 'movie-library' ),
			'update_item'                => __( 'Update Production Company', 'movie-library' ),
			'add_new_item'               => __( 'Add New Production Company', 'movie-library' ),
			'new_item_name'              => __( 'New Production Company Name', 'movie-library' ),
			'menu_name'                  => __( 'Production Company', 'movie-library' ),
			'view_item'                  => __( 'View Production Company', 'movie-library' ),
			'popular_items'              => __( 'Popular Production Company', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Production Companies with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove Production Companies', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular Production Companies', 'movie-library' ),
			'not_found'                  => __( 'No Production Companies found', 'movie-library' ),
		);
			register_taxonomy(
				'rt-movie-production-company',
				self::RT_MOVIE_SLUG,
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_production_companies',
						'edit_terms'   => 'edit_production_companies',
						'delete_terms' => 'delete_production_companies',
						'assign_terms' => 'assign_production_companies',
					),
				)
			);
	}

	/**
	 * Create tags taxonomy for rt-movie post type.
	 *
	 * @return void
	 */
	public function create_movie_tags_taxonomy() {
		$labels = array(
			'name'                       => __( 'Tags', 'movie-library' ),
			'singular_name'              => __( 'Movie Tag', 'movie-library' ),
			'search_items'               => __( 'Search Movie Tags', 'movie-library' ),
			'all_items'                  => __( 'All Movie Tags', 'movie-library' ),
			'edit_item'                  => __( 'Edit Movie Tag', 'movie-library' ),
			'update_item'                => __( 'Update Movie Tag', 'movie-library' ),
			'add_new_item'               => __( 'Add New Movie Tag', 'movie-library' ),
			'new_item_name'              => __( 'New Movie Tag Name', 'movie-library' ),
			'menu_name'                  => __( 'Movie Tag', 'movie-library' ),
			'view_item'                  => __( 'View Movie Tag', 'movie-library' ),
			'popular_items'              => __( 'Popular Movie Tag', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Movie Tags with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove Movie Tags', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular Movie Tags', 'movie-library' ),
			'not_found'                  => __( 'No Movie Tags found', 'movie-library' ),
		);
			register_taxonomy(
				'rt-movie-tag',
				self::RT_MOVIE_SLUG,
				array(
					'hierarchical'      => false,
					'labels'            => $labels,
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_movie_tags',
						'edit_terms'   => 'edit_movie_tags',
						'delete_terms' => 'delete_movie_tags',
						'assign_terms' => 'assign_movie_tags',
					),
				)
			);
	}

	/**
	 * Create shadow taxonomy person for rt-movie post type.
	 *
	 * @return void
	 */
	public function create_shadow_movie_person_taxonomy() {
		$labels = array(
			'name' => __( 'People', 'movie-library' ),
		);
			register_taxonomy(
				'_rt-movie-person',
				self::RT_MOVIE_SLUG,
				array(
					'hierarchical'      => false,
					'labels'            => $labels,
					'public'            => false,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => false,
					'show_ui'           => true,
				)
			);
	}

	/**
	 * Wrapper function for calling all create meta boxes function if post type is rt-movie.
	 *
	 * @return void
	 */
	public function create_meta_boxes() {
		if ( get_post_type() === self::RT_MOVIE_SLUG ) {
			$this->create_crew_meta_box();
			$this->create_movie_meta_basic_metabox();
		}
	}

	/**
	 * Register basic meta data meta box for rt-movie
	 *
	 * @return void
	 */
	public function create_movie_meta_basic_metabox() {
		add_meta_box(
			'rt-movie-meta-basic',
			__( 'Basic', 'movie-library' ),
			array( $this, 'movie_meta_box_html' ),
			array( self::RT_MOVIE_SLUG ),
			'side'
		);
	}

	/**
	 * Create HTML structure for movie meta data meta box
	 *
	 * @param /WP_Query $post the global $post variable.
	 * @return void
	 */
	public function movie_meta_box_html( $post ) {
		$post_meta_data = get_movie_post_meta( $post->ID, 'rt-movie-meta-basic', true );
		$age_rating     = get_movie_post_meta( $post->ID, 'rt-movie-age-rating', true );
		$data_arr       = array(
			'rt-movie-meta-basic-rating'       => $post_meta_data['rt-movie-meta-basic-rating'] ?? '',
			'rt-movie-meta-basic-runtime'      => $post_meta_data['rt-movie-meta-basic-runtime'] ?? '',
			'rt-movie-meta-basic-release-date' => $post_meta_data['rt-movie-meta-basic-release-date'] ?? '',
		);
		wp_nonce_field( 'rt-movie-nonce', 'rt-movie-nonce' );
		?>
		<label for="rt-movie-meta-basic-rating">
			<?php
				esc_html_e( 'Movie Rating', 'movie-library' );
			?>
		</label>	
		<input type="number" name="rt-movie-meta-basic-rating" id="rt-movie-meta-basic-rating" value=<?php echo esc_attr( $data_arr['rt-movie-meta-basic-rating'] ); ?>>
		<br>
		<label for="rt-movie-meta-basic-runtime">
			<?php
				esc_html_e( 'Movie Runtime', 'movie-library' );
			?>
		</label>
		<input type="text" name="rt-movie-meta-basic-runtime" id="rt-movie-meta-basic-runtime" value="<?php echo esc_attr( $data_arr['rt-movie-meta-basic-runtime'] ); ?>">
		<br>
		<label for="rt-movie-meta-basic-release-date">
			<?php
				esc_html_e( 'Movie Release Date', 'movie-library' );
			?>
		</label>
		<input type="date" name="rt-movie-meta-basic-release-date" id="rt-movie-meta-basic-release-date" value=<?php echo esc_attr( $data_arr['rt-movie-meta-basic-release-date'] ); ?>>
		<br>
		<label for="rt-movie-age-rating">
			<?php
				esc_html_e( 'Movie Age Rating', 'movie-library' );
			?>
		</label>
		<input type="text" name="rt-movie-age-rating" id="rt-movie-age-rating" value=<?php echo esc_attr( $age_rating ); ?>>

		<?php
	}

	/**
	 * Save the meta data from basic meta data meta boxes to database
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_meta_box_values( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['rt-movie-nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( $_POST['rt-movie-nonce'] ), 'rt-movie-nonce' ) ) {
				return;
			}
		}
		$rt_movie_meta_basic = [ 'rt-movie-meta-basic-rating', 'rt-movie-meta-basic-runtime', 'rt-movie-meta-basic-release-date' ];
		$post_data           = array();
		foreach ( $rt_movie_meta_basic as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$post_data += [ $field => sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) ];
			}
		}
		if ( ! empty( $post_data ) ) {
			update_movie_post_meta( $post_id, 'rt-movie-meta-basic', $post_data );
		}
		if ( isset( $_POST['rt-movie-age-rating'] ) ) {
			$age_rating = sanitize_text_field( wp_unslash( $_POST['rt-movie-age-rating'] ) );
			if ( $age_rating ) {
				update_movie_post_meta( $post_id, 'rt-movie-age-rating', $age_rating );
			}
		}
	}

	/**
	 * Register crew meta box
	 *
	 * @return void
	 */
	public function create_crew_meta_box() {
		add_meta_box(
			'rt-movie-meta-crew',
			__( 'Crew', 'movie-library' ),
			array( $this, 'movie_crew_meta_box_html' ),
			array( 'rt-movie' ),
			'side'
		);
	}

	/**
	 * Get meta data already stored from the db.
	 *
	 * @param string $type the crew type name eg. director, actor.
	 * @return array
	 */
	public function get_stored_crew_data( $type ) {
		$crew         = new WP_Query(
			[
				'post_type' => Rt_Person::RT_PERSON_SLUG,
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'tax_query' => [
					[
						'taxonomy' => 'rt-person-career',
						'field'    => 'slug',
						'terms'    => $type,
					],
				],
			]
		);
		$crew_from_db = array();
		foreach ( $crew->posts as $person ) {
			$crew_from_db[] = array(
				'id'   => $person->ID,
				'name' => $person->post_title,
			);
		}
		return $crew_from_db;
	}

	/**
	 * Create the HTML structure for crew meta box
	 *
	 * @param /WP_Query $post the global $post object.
	 * @return void
	 */
	public function movie_crew_meta_box_html( $post ) {
		$director_from_db   = $this->get_stored_crew_data( 'director' );
		$director_data      = array();
		$director_meta_data = get_movie_post_meta( $post->ID, 'rt-movie-meta-crew-director', true );
		if ( $director_meta_data ) {
			foreach ( $director_meta_data as $director ) {
				array_push( $director_data, $director );
			}
		}
		wp_nonce_field( 'rt-movie-nonce', 'rt-movie-nonce' );
		?>
		<label for='rt-movie-meta-crew-director'><?php esc_html_e( 'Director', 'movie-library' ); ?></label>
		<select name="rt-movie-meta-crew-director[]" id="rt-movie-meta-crew-director" multiple>
			<?php
			foreach ( $director_from_db as $dir ) {
				?>
					<option value=<?php echo esc_attr( $dir['id'] ); ?> <?php selected( in_array( absint( $dir['id'] ), $director_data, true ), true ); ?>  ><?php echo esc_html( $dir['name'] ); ?></option>
				<?php
			}
			?>
		</select>
		<br>	
		<?php
		$producer_from_db   = $this->get_stored_crew_data( 'producer' );
		$producer_data      = array();
		$producer_meta_data = get_movie_post_meta( $post->ID, 'rt-movie-meta-crew-producer', true );
		if ( $producer_meta_data ) {
			foreach ( $producer_meta_data as $producer ) {
				array_push( $producer_data, $producer );
			}
		}
		?>
		<label for='rt-movie-meta-crew-producer'><?php esc_html_e( 'Producer', 'movie-library' ); ?></label>
		<select name="rt-movie-meta-crew-producer[]" id="rt-movie-meta-crew-producer" multiple>
			<?php
			foreach ( $producer_from_db as $pro ) {
				?>
					<option value=<?php echo esc_attr( $pro['id'] ); ?> <?php selected( in_array( absint( $pro['id'] ), $producer_data, true ), true ); ?> ><?php echo esc_html( $pro['name'] ); ?></option>
				<?php
			}
			?>
		</select>
		<br>
		<?php

		$writer_from_db   = $this->get_stored_crew_data( 'writer' );
		$writer_data      = array();
		$writer_meta_data = get_movie_post_meta( $post->ID, 'rt-movie-meta-crew-writer', true );
		if ( $writer_meta_data ) {
			foreach ( $writer_meta_data as $writer ) {
				array_push( $writer_data, $writer );
			}
		}
		?>
		<label for='rt-movie-meta-crew-writer'><?php esc_html_e( 'Writer', 'movie-library' ); ?></label>
		<select name="rt-movie-meta-crew-writer[]" id="rt-movie-meta-crew-writer" multiple>
			<?php
			foreach ( $writer_from_db as $wir ) {
				?>
					<option value=<?php echo esc_attr( $wir['id'] ); ?> <?php selected( in_array( absint( $wir['id'] ), $writer_data, true ), true ); ?> >
						<?php echo esc_html( $wir['name'] ); ?>
					</option>
				<?php
			}
			?>
		</select>
		<br>
		<?php

		$actor_from_db       = $this->get_stored_crew_data( 'actor' );
		$actor_data          = array();
		$actor_meta_data     = get_movie_post_meta( $post->ID, 'rt-movie-meta-crew-actor', true );
		$character_meta_data = get_movie_post_meta( $post->ID, 'rt-movie-character', true );
		if ( $actor_meta_data ) {
			foreach ( $actor_meta_data as $actor ) {
				array_push( $actor_data, $actor );
			}
		}
		$actor_data_string = implode( ',', $actor_data );
		?>
		<label for='rt-movie-meta-crew-actor'><?php esc_html_e( 'Actor', 'movie-library' ); ?></label>
		<select name="rt-movie-meta-crew-actor[]" id="rt-movie-meta-crew-actor" multiple>
			<?php
			foreach ( $actor_from_db as $act ) {
				?>
					<option value=<?php echo esc_attr( $act['id'] ); ?> <?php selected( in_array( absint( $act['id'] ), $actor_data, true ), true ); ?> ><?php echo esc_html( $act['name'] ); ?></option>
				<?php
			}
			?>
		</select>
		<br>
		<?php
		foreach ( $actor_data as $actor ) {
			$actor_name = get_post( $actor );
			$name_in_db = '';
			if ( $character_meta_data ) {
				$name_in_db = $character_meta_data[ $actor ];
			}
			?>
			<label for="<?php echo esc_attr( $actor_name->post_title ); ?>"><?php echo esc_attr( $actor_name->post_title ); ?></label>
			<input type="text" name="<?php echo esc_attr( "actor-{$actor}" ); ?>" id="<?php echo esc_attr( $actor_name->post_title ); ?>" value="<?php echo esc_attr( $name_in_db ); ?>">
			<br>
			<?php
		}
		?>
		<input type='hidden' id='hidden-actors' name='hidden-actors' value=<?php echo esc_attr( $actor_data_string ); ?>>
		<?php
	}

	/**
	 * Save data from crew meta boxes to database
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_crew_meta_box_values( $post_id ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$nonce = '';

		if ( isset( $_POST['rt-movie-nonce'] ) ) {
			$nonce = sanitize_key( $_POST['rt-movie-nonce'] );
		}

		$fields = array( 'rt-movie-meta-crew-director', 'rt-movie-meta-crew-actor', 'rt-movie-meta-crew-writer', 'rt-movie-meta-crew-producer' );

		if ( ! wp_verify_nonce( $nonce, 'rt-movie-nonce' ) ) {
			return;
		}

		wp_delete_object_term_relationships( $post_id, '_rt-movie-person' );

		$unique_person_ids = array();

		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$sanitized_field = array_map( 'absint', $_POST[ $field ] );
				if ( empty( $sanitized_field ) ) {
					delete_movie_post_meta( $post_id, $sanitized_field );
					continue;
				}
				foreach ( $sanitized_field as $person_id ) {
					$unique_person_ids[] = $person_id;
				}
				update_movie_post_meta( $post_id, $field, $sanitized_field );
			}
		}

		$unique_person_ids = array_unique( $unique_person_ids );

		$this->create_shadow_taxonomy( $unique_person_ids, $post_id );

		$actors_to_save = get_movie_post_meta( $post_id, 'rt-movie-meta-crew-actor', true );
		$char_arr       = array();
		foreach ( $actors_to_save as $act_id ) {
			if ( ! empty( $_POST[ "actor-{$act_id}" ] ) ) {
				$name      = sanitize_text_field( wp_unslash( $_POST[ "actor-{$act_id}" ] ) );
				$char_arr += array( $act_id => $name );
			}
		}
		if ( ! empty( $char_arr ) ) {
			update_movie_post_meta( $post_id, 'rt-movie-character', $char_arr );
		}
	}

	/**
	 * Create the shadow taxonomy terms for person linking to its movies
	 *
	 * @param array $creators array of creators.
	 * @param int   $post_id the post_id of the current post.
	 * @return void
	 */
	public function create_shadow_taxonomy( $creators, $post_id ) {
		$creator_terms = array();
		foreach ( $creators as $creator_id ) {
			$creator_terms[] = sprintf( 'person-%d', $creator_id );
		}
		wp_set_object_terms( $post_id, $creator_terms, '_rt-movie-person', true );

	}
}
