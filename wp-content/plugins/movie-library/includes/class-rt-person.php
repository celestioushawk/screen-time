<?php
/**
 * Create the rt-person post type, career taxonomy and rt-person meta boxes to save basic and social meta data about person
 *
 * @package movie-library
 */

namespace Includes\Class_Rt_Person;

/**
 * Create the rt-person post type and its taxonomies along with its meta boxes
 */
class Rt_Person {
	public const RT_PERSON_SLUG = 'rt-person';

	public const RT_PERSON_CAREER = 'rt-person-career';
	/**
	 * Initialize the class and connect the call back functions to their respective hooks
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'rt_person_init' ] );
		add_action( 'init', [ $this, 'create_person_career_taxonomy' ] );
		add_action( 'add_meta_boxes', [ $this, 'create_meta_boxes' ] );
		add_action( 'save_post_rt-person', [ $this, 'save_person_basic_meta_data' ] );
		add_action( 'save_post_rt-person', [ $this, 'save_person_social_meta_data' ] );
		add_action( 'save_post_rt-person', [ $this, 'save_extra_info_meta_data' ] );
	}

	/**
	 * Wrapper function for calling meta boxes functions.
	 *
	 * @return void
	 */
	public function create_meta_boxes() {
		if ( get_post_type() === self::RT_PERSON_SLUG ) {
			$this->create_person_meta_basic_meta_box();
			$this->create_person_meta_social_meta_box();
			$this->create_extra_person_info_metabox();
		}
	}

	/**
	 * Creates the rt-person post type
	 *
	 * @return void
	 */
	public function rt_person_init() {
		register_post_type(
			'rt-person',
			[
				'labels'            => [
					'name'                  => __( 'People', 'movie-library' ),
					'singular_name'         => __( 'Person', 'movie-library' ),
					'all_items'             => __( 'All People', 'movie-library' ),
					'archives'              => __( 'Person Archives', 'movie-library' ),
					'attributes'            => __( 'Person Attributes', 'movie-library' ),
					'insert_into_item'      => __( 'Insert into Person', 'movie-library' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Person', 'movie-library' ),
					'featured_image'        => _x( 'Profile Photo', 'Profile photo of person', 'movie-library' ),
					'set_featured_image'    => _x( 'Set profile photo', 'Set Profile photo of person', 'movie-library' ),
					'remove_featured_image' => _x( 'Remove profile photo', 'Remove Profile photo of person', 'movie-library' ),
					'use_featured_image'    => _x( 'Use as profile photo', 'Use as Profile photo of person', 'movie-library' ),
					'filter_items_list'     => __( 'Filter Person list', 'movie-library' ),
					'items_list_navigation' => __( 'People list navigation', 'movie-library' ),
					'items_list'            => __( 'People list', 'movie-library' ),
					'new_item'              => __( 'New Person', 'movie-library' ),
					'add_new'               => __( 'Add New', 'movie-library' ),
					'add_new_item'          => __( 'Add New Person', 'movie-library' ),
					'edit_item'             => __( 'Edit Person', 'movie-library' ),
					'view_item'             => __( 'View Person', 'movie-library' ),
					'view_items'            => __( 'View People', 'movie-library' ),
					'search_items'          => __( 'Search People', 'movie-library' ),
					'not_found'             => __( 'No People found', 'movie-library' ),
					'not_found_in_trash'    => __( 'No People found in trash', 'movie-library' ),
					'parent_item_colon'     => __( 'Parent Movie:', 'movie-library' ),
					'menu_name'             => __( 'People', 'movie-library' ),
				],
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author' ],
				'has_archive'       => true,
				'query_var'         => true,
				'menu_position'     => null,
				'menu_icon'         => 'dashicons-businessperson',
				'show_in_rest'      => true,
				'capability_type'   => array( 'person', 'people' ),
				'map_meta_cap'      => true,
			]
		);
	}

	/**
	 * Create the rt-person-career taxonomy for rt-person
	 *
	 * @return void
	 */
	public function create_person_career_taxonomy() {
		$labels = array(
			'name'                       => __( 'Person Career', 'movie-library' ),
			'singular_name'              => __( 'Person career', 'movie-library' ),
			'search_items'               => __( 'Search Person career', 'movie-library' ),
			'all_items'                  => __( 'All Person career', 'movie-library' ),
			'edit_item'                  => __( 'Edit Person career', 'movie-library' ),
			'update_item'                => __( 'Update Person career', 'movie-library' ),
			'add_new_item'               => __( 'Add New Person career', 'movie-library' ),
			'new_item_name'              => __( 'New Person career', 'movie-library' ),
			'menu_name'                  => __( 'Person career', 'movie-library' ),
			'view_item'                  => __( 'View Person career', 'movie-library' ),
			'popular_items'              => __( 'Popular Person career', 'movie-library' ),
			'separate_items_with_commas' => __( 'Separate Person career with commas', 'movie-library' ),
			'add_or_remove_items'        => __( 'Add or remove Person career', 'movie-library' ),
			'choose_from_most_used'      => __( 'Choose from the most popular Person career', 'movie-library' ),
			'not_found'                  => __( 'No Person career found', 'movie-library' ),
		);
			register_taxonomy(
				'rt-person-career',
				self::RT_PERSON_SLUG,
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_person_careers',
						'edit_terms'   => 'edit_person_careers',
						'delete_terms' => 'delete_person_careers',
						'assign_terms' => 'assign_person_careers',
					),
				)
			);
	}

	/**
	 * Initialize the meta basic metabox to get the basic meta data for person
	 *
	 * @return void
	 */
	public function create_person_meta_basic_meta_box() {
		add_meta_box(
			'rt-person-meta-basic',
			__( 'Basic', 'movie-library' ),
			array( $this, 'person_basic_meta_box_html' ),
			array( self::RT_PERSON_SLUG ),
			'side'
		);
	}

	/**
	 * Create the rt-person-basic-meta metaboxes
	 *
	 * @return void
	 */
	public function create_person_meta_social_meta_box() {
		add_meta_box(
			'rt-person-meta-social',
			__( 'Social', 'movie-library' ),
			array( $this, 'person_social_meta_box_html' ),
			array( self::RT_PERSON_SLUG ),
			'side'
		);
	}

	/**
	 * Create the rt-person-full-info metabox
	 *
	 * @return void
	 */
	public function create_extra_person_info_metabox() {
		add_meta_box(
			'rt-person-full-info',
			__( 'More Information', 'movie-library' ),
			array( $this, 'extra_person_info_metabox_html' ),
			array( self::RT_PERSON_SLUG ),
			'side',
		);
	}

	/**
	 * Create the html structure for the rt-person-basic meta data metaboxes
	 *
	 * @param \WP_Post $post The global $post object.
	 * @return void
	 */
	public function person_basic_meta_box_html( $post ) {
		$stored_meta_data = get_person_post_meta( $post->ID, 'rt-person-meta-basic', true );
		$data_arr         = array(
			'rt-person-meta-basic-birth-date'  => $stored_meta_data['rt-person-meta-basic-birth-date'] ?? '',
			'rt-person-meta-basic-birth-place' => $stored_meta_data['rt-person-meta-basic-birth-place'] ?? '',
		);
		?>
			<label for="rt-person-meta-basic-birth-date">
				<?php
					esc_html_e( 'Birth Date', 'movie-library' );
				?>
			</label>
			<br>
			<input type="date" name="rt-person-meta-basic-birth-date" id="rt-person-meta-basic-birth-date" value=<?php echo esc_attr( $data_arr['rt-person-meta-basic-birth-date'] ); ?>>
			<br>
			<label for="rt-person-meta-basic-birth-place">
				<?php
					esc_html_e( 'Birth Place', 'movie-library' );
				?>
			</label>
			<br>
			<input type="text" name="rt-person-meta-basic-birth-place" id="rt-person-meta-basic-birth-place" value="<?php echo esc_attr( $data_arr['rt-person-meta-basic-birth-place'] ); ?>">

		<?php
	}

	/**
	 * Create the html structure for rt-person-social meta data metaboxes
	 *
	 * @param \WP_Post $post The post object of the post being saved.
	 * @return void
	 */
	public function person_social_meta_box_html( $post ) {
		$stored_meta_data = get_person_post_meta( $post->ID, 'rt-person-meta-social', true );
		$data_arr         = array(
			'rt-person-meta-social-twitter'   => $stored_meta_data['rt-person-meta-social-twitter'] ?? '',
			'rt-person-meta-social-facebook'  => $stored_meta_data['rt-person-meta-social-facebook'] ?? '',
			'rt-person-meta-social-instagram' => $stored_meta_data['rt-person-meta-social-instagram'] ?? '',
			'rt-person-meta-social-web'       => $stored_meta_data['rt-person-meta-social-web'] ?? '',
		);
		wp_nonce_field( 'rt-person-meta-social', 'rt-person-meta-social' );
		?>
		<label for="rt-person-meta-social-twitter">
			<?php
				esc_html_e( 'Twitter', 'movie-library' );
			?>
		</label>
		<br>
		<input type="url" name="rt-person-meta-social-twitter" id="rt-person-meta-social-twitter" value=<?php echo esc_attr( $data_arr['rt-person-meta-social-twitter'] ); ?>>
		<br>

		<label for="rt-person-meta-social-facebook">
			<?php
				esc_html_e( 'Facebook', 'movie-library' );
			?>
		</label>
		<br>
		<input type="url" name="rt-person-meta-social-facebook" id="rt-person-meta-social-facebook" value=<?php echo esc_attr( $data_arr['rt-person-meta-social-facebook'] ); ?>>
		<br>

		<label for="rt-person-meta-social-instagram">
			<?php
				esc_html_e( 'Instagram', 'movie-library' );
			?>
		</label>
		<br>
		<input type="url" name="rt-person-meta-social-instagram" id="rt-person-meta-social-instagram" value=<?php echo esc_attr( $data_arr['rt-person-meta-social-instagram'] ); ?>>
		<br>

		<label for="rt-person-meta-social-web">
			<?php
				esc_html_e( 'Web', 'movie-library' );
			?>
		</label>
		<br>
		<input type="url" name="rt-person-meta-social-web" id="rt-person-meta-social-web" value=<?php echo esc_attr( $data_arr['rt-person-meta-social-web'] ); ?>>
		<br>
		<?php
	}

	/**
	 * Create the html structure for rt-person extra info meta data metaboxes
	 *
	 * @param \WP_Post $post The post object of the post being saved.
	 * @return void
	 */
	public function extra_person_info_metabox_html( $post ) {
		$stored_full_name         = get_person_post_meta( $post->ID, 'rt-person-full-name', true );
		$stored_person_debut_year = get_person_post_meta( $post->ID, 'rt-person-debut-year', true );
		$stored_debut_movie       = get_person_post_meta( $post->ID, 'rt-person-debut-movie', true );
		$stored_debut_movie_year  = get_person_post_meta( $post->ID, 'rt-person-debut-movie-year', true );
		?>
		<label for="rt-person-full-name">
			<?php
				esc_html_e( 'Full Name', 'movie-library' );
			?>
		</label>
		<br>
		<input type="text" name="rt-person-full-name" id="rt-person-full-name" value="<?php echo esc_attr( $stored_full_name ); ?>">
		<br>


		<label for="rt-person-debut-year">
			<?php
				esc_html_e( 'Person Debut Year', 'movie-library' );
			?>
		</label>
		<br>
		<input type="text" name="rt-person-debut-year" id="rt-person-debut-year" value="<?php echo esc_attr( $stored_person_debut_year ); ?>">
		<br>


		<label for="rt-person-debut-movie">
			<?php
				esc_html_e( 'Debut Movie', 'movie-library' );
			?>
		</label>
		<br>
		<input type="text" name="rt-person-debut-movie" id="rt-person-debut-movie" value="<?php echo esc_attr( $stored_debut_movie ); ?>">
		<br>


		<label for="rt-person-debut-movie-year">
			<?php
				esc_html_e( 'Debut Movie Year', 'movie-library' );
			?>
		</label>
		<br>
		<input type="text" name="rt-person-debut-movie-year" id="rt-person-debut-movie-year" value="<?php echo esc_attr( $stored_debut_movie_year ); ?>">
		<?php
	}

	/**
	 * Saves the person full name, debut movie, debut year and debut movie year to the database
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_extra_info_meta_data( $post_id ) {
		if ( isset( $_POST['rt-person-meta-social'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( $_POST['rt-person-meta-social'] ), 'rt-person-meta-social' ) ) {
				return;
			}
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$extra_meta_fields = array( 'rt-person-full-name', 'rt-person-debut-year', 'rt-person-debut-movie', 'rt-person-debut-movie-year' );

		foreach ( $extra_meta_fields as $meta_field ) {
			if ( isset( $_POST[ $meta_field ] ) ) {
				$meta_field_value = sanitize_text_field( wp_unslash( $_POST[ $meta_field ] ) );
				if ( ! empty( $meta_field_value ) ) {
					update_person_post_meta( $post_id, $meta_field, $meta_field_value );
				}
			}
		}

	}

	/**
	 * Saves the basic meta data from the form to the database
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_person_basic_meta_data( $post_id ) {
		if ( isset( $_POST['rt-person-meta-social'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( $_POST['rt-person-meta-social'] ), 'rt-person-meta-social' ) ) {
				return;
			}
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['rt-person-meta-basic-birth-date'] ) && isset( $_POST['rt-person-meta-basic-birth-place'] ) ) {
				$post_data = array(
					'rt-person-meta-basic-birth-date'  => sanitize_text_field( wp_unslash( $_POST['rt-person-meta-basic-birth-date'] ) ),
					'rt-person-meta-basic-birth-place' => sanitize_text_field( wp_unslash( $_POST['rt-person-meta-basic-birth-place'] ) ),
				);
				if ( ! empty( $post_data ) ) {
					update_person_post_meta( $post_id, 'rt-person-meta-basic', $post_data );
				}
		}

	}

	/**
	 * Saves the social meta data from the form to the database
	 *
	 * @param int $post_id The post id of the post being saved.
	 * @return void
	 */
	public function save_person_social_meta_data( $post_id ) {
		if ( isset( $_POST['rt-person-meta-social'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( $_POST['rt-person-meta-social'] ), 'rt-person-meta-social' ) ) {
				return;
			}
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['rt-person-meta-social-twitter'] ) && isset( $_POST['rt-person-meta-social-facebook'] ) && isset( $_POST['rt-person-meta-social-instagram'] ) && isset( $_POST['rt-person-meta-social-web'] ) ) {
				$twitter   = esc_url_raw( wp_unslash( $_POST['rt-person-meta-social-twitter'] ) );
				$facebook  = esc_url_raw( wp_unslash( $_POST['rt-person-meta-social-facebook'] ) );
				$instagram = esc_url_raw( wp_unslash( $_POST['rt-person-meta-social-instagram'] ) );
				$web       = esc_url_raw( wp_unslash( $_POST['rt-person-meta-social-web'] ) );
				$post_data = array(
					'rt-person-meta-social-twitter'   => $twitter,
					'rt-person-meta-social-facebook'  => $facebook,
					'rt-person-meta-social-instagram' => $instagram,
					'rt-person-meta-social-web'       => $web,
				);
		}
		if ( ! empty( $post_data ) ) {
			update_person_post_meta( $post_id, 'rt-person-meta-social', $post_data );
		}
	}

}
