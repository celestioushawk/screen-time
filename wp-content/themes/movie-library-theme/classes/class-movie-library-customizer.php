<?php
/**
 * Implement customizer panels, sections, settings and controls to change different theme settings of the site
 * and make it dynamic.
 *
 * @package movie-library-theme
 */

use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Rt_Person\Rt_Person;

/**
 * Implement custom customizer panel, sections and controls using Customizer API.
 */
class Movie_Library_Customizer {
	/**
	 * Function to hook our register function to the customize_register hook.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'ml_customize_register' ) );
	}

	/**
	 * Register panels, sections, settings and controls for Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Class object that controls the Theme Customizer screen.
	 * @return void
	 */
	public function ml_customize_register( WP_Customize_Manager $wp_customize ) {

		$wp_customize->add_panel(
			'ml_customize_panel',
			array(
				'title'           => __( 'Movie Library Theme Settings', 'movie-library-theme' ),
				'description'     => __( 'Panel for customizing the activated theme', 'movie-library-theme' ),
				'priority'        => 160,
				'active_callback' => array( $this, 'ml_customize_panel_active_callback' ),
			)
		);

		$this->ml_customize_body_color( $wp_customize );

		$this->ml_customize_posts_navigation( $wp_customize );

		$this->ml_customize_movie_details( $wp_customize );

		$this->ml_change_media_sizes( $wp_customize );

	}

	/**
	 * Register section, settings and controls for changing body background color.
	 *
	 * @param WP_Customize_Manager $wp_customize Class object that controls the Theme Customizer screen.
	 * @return void
	 */
	public function ml_customize_body_color( $wp_customize ) {

		$wp_customize->add_section(
			'ml_background_color_section',
			array(
				'title'    => __( 'Change Site Background Color', 'movie-library-theme' ),
				'priority' => 105,
				'panel'    => 'ml_customize_panel',

			)
		);

		$wp_customize->add_setting(
			'ml_body_background_color',
			array(
				'default'           => '#1f1f1f',
				'sanitize_callback' => 'sanitize_hex_color',
				'section'           => 'ml_background_color_section',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'ml_body_background_color',
				array(
					'label'    => __( 'Choose Color', 'movie-library-theme' ),
					'section'  => 'ml_background_color_section',
					'settings' => 'ml_body_background_color',

				)
			)
		);
	}

	/**
	 * Register section, settings and controls for customizing posts navigation.
	 *
	 * @param WP_Customize_Manager $wp_customize Class object that controls the Theme Customizer screen.
	 * @return void
	 */
	public function ml_customize_posts_navigation( $wp_customize ) {

		$wp_customize->add_section(
			'ml_display_posts_navigation',
			array(
				'title'           => __( 'Posts Navigation Settings', 'movie-library-theme' ),
				'priority'        => 105,
				'panel'           => 'ml_customize_panel',
				'active_callback' => array( $this, 'ml_single_page_section_callback' ),

			)
		);

		$wp_customize->add_setting(
			'ml_single_posts_navigation',
			array(
				'default'           => true,
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_display_posts_navigation',
			)
		);

		$wp_customize->add_control(
			'ml_single_posts_navigation',
			array(
				'label'       => __( 'Display Posts Navigation', 'movie-library-theme' ),
				'section'     => 'ml_display_posts_navigation',
				'settings'    => 'ml_single_posts_navigation',
				'description' => __( 'Choose whether to display posts navigation', 'movie-library-theme' ),
				'type'        => 'radio',
				'choices'     => array(
					1 => __( 'Yes', 'movie-library-theme' ),
					0 => __( 'No', 'movie-library-theme' ),

				),
			)
		);
	}

	/**
	 * Register section, settings, controls for customizing movie details display format.
	 *
	 * @param WP_Customize_Manager $wp_customize Class object that controls the Theme Customizer screen.
	 * @return void
	 */
	public function ml_customize_movie_details( $wp_customize ) {

		$wp_customize->add_section(
			'ml_display_movie_details',
			array(
				'title'           => __( 'Change Movie Settings', 'movie-library-theme' ),
				'priority'        => 105,
				'panel'           => 'ml_customize_panel',
				'active_callback' => array( $this, 'ml_is_movie_page_callback' ),

			)
		);

		$wp_customize->add_setting(
			'ml_time_format',
			array(
				'default'           => true,
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_display_movie_details',
			)
		);

		$wp_customize->add_control(
			'ml_time_format',
			array(
				'label'       => __( 'Time Display Format', 'movie-library-theme' ),
				'section'     => 'ml_display_movie_details',
				'settings'    => 'ml_time_format',
				'type'        => 'radio',
				'description' => __( 'Choose whether to display time in HH:MM or minutes format', 'movie-library-theme' ),
				'choices'     => array(
					'HHMM'    => __( 'HH:MM', 'movie-library-theme' ),
					'minutes' => __( 'Minutes', 'movie-library-theme' ),
				),
			)
		);

		$wp_customize->add_setting(
			'ml_separator',
			array(
				'default'           => 'circle',
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_display_movie_details',
			)
		);

		$wp_customize->add_control(
			'ml_separator',
			array(
				'label'       => __( 'Display Separator', 'movie-library-theme' ),
				'section'     => 'ml_display_movie_details',
				'settings'    => 'ml_separator',
				'type'        => 'radio',
				'choices'     => array(
					'circle' => __( 'Circle', 'movie-library-theme' ),
					'hyphen' => __( 'Hyphen', 'movie-library-theme' ),
				),
				'description' => __( 'Choose whether to display separator as " - " or " • "', 'movie-library-theme' ),
			)
		);
	}

	/**
	 * Register section, settings, controls for customzing sizes for image and sidebar.
	 *
	 * @param WP_Customize_Manager $wp_customize Class object that controls the Theme Customizer screen.
	 * @return void
	 */
	public function ml_change_media_sizes( $wp_customize ) {

		$wp_customize->add_section(
			'ml_size_customizer',
			array(
				'title'           => __( 'Change Single Page Settings', 'movie-library-theme' ),
				'priority'        => 105,
				'panel'           => 'ml_customize_panel',
				'active_callback' => array( $this, 'ml_single_page_section_callback' ),
			)
		);

		$wp_customize->add_setting(
			'ml_change_image_height',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_size_customizer',
			)
		);

		$wp_customize->add_control(
			'ml_change_image_height',
			array(
				'label'       => __( 'Featured Image Height', 'movie-library-theme' ),
				'section'     => 'ml_size_customizer',
				'settings'    => 'ml_change_image_height',
				'type'        => 'number',
				'description' => __( 'Set the height of the featured image (px)', 'movie-library-theme' ),
			)
		);

		$wp_customize->add_setting(
			'ml_change_image_width',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_size_customizer',
			)
		);

		$wp_customize->add_control(
			'ml_change_image_width',
			array(
				'label'       => __( 'Featured Image Width', 'movie-library-theme' ),
				'section'     => 'ml_size_customizer',
				'settings'    => 'ml_change_image_width',
				'type'        => 'number',
				'description' => __( 'Set the width of the featured image (px, %, rem)', 'movie-library-theme' ),
			)
		);

		$wp_customize->add_setting(
			'ml_change_sidebar_width',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_size_customizer',
			)
		);

		$wp_customize->add_control(
			'ml_change_sidebar_width',
			array(
				'label'       => __( 'Sidebar Width', 'movie-library-theme' ),
				'section'     => 'ml_size_customizer',
				'settings'    => 'ml_change_sidebar_width',
				'type'        => 'number',
				'description' => __( 'Set the width of the theme sidebars (px, %, rem)', 'movie-library-theme' ),
			)
		);
		$wp_customize->add_setting(
			'ml_set_sidebar_width_unit',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'section'           => 'ml_size_customizer',
			)
		);

		$wp_customize->add_control(
			'ml_set_sidebar_width_unit',
			array(
				'label'       => __( 'Sidebar Width Unit', 'movie-library-theme' ),
				'default'     => 'px',
				'section'     => 'ml_size_customizer',
				'settings'    => 'ml_set_sidebar_width_unit',
				'description' => __( 'Set the unit of the width of theme sidebars', 'movie-library-theme' ),
				'type'        => 'radio',
				'choices'     => array(
					'px'  => __( 'px', 'movie-library-theme' ),
					'%'   => __( '%', 'movie-library-theme' ),
					'rem' => __( 'rem', 'movie-library-theme' ),
				),
			)
		);
	}

	/**
	 * Check if the current page being displayed is a single post or archive page of rt-movie
	 * rt-person post type.
	 *
	 * @return bool true if singular/archive page | false if not.
	 */
	public function ml_customize_panel_active_callback() {

		return is_singular( array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG ) ) ||
		is_post_type_archive( array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG ) );

	}

	/**
	 * Check if the current page being displayed is a singular page of rt-movie
	 * rt-person post type.
	 *
	 * @return bool true if singular page | false if not.
	 */
	public function ml_single_page_section_callback() {
		return is_singular( array( Rt_Movie::RT_MOVIE_SLUG, Rt_Person::RT_PERSON_SLUG ) );
	}

	/**
	 * Check if current page being displayed is of singular type of rt-movie post type.
	 *
	 * @return bool true if singular page | false if not.
	 */
	public function ml_is_movie_page_callback() {
		return is_singular( Rt_Movie::RT_MOVIE_SLUG );
	}
}
