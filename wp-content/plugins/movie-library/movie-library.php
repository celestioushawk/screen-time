<?php
/**
 * The main class for the plugin which instantiates all the other classes.
 *
 * @package movie-library
 */

/**
 * Plugin Name:     Movie Library
 * Plugin URI:      https://github.com/celestioushawk
 * Description:     A movies library plugin which allows you to add movies and manage movie related data.
 * Author:          Piyush Tekwani
 * Author URI:      https://github.com/celestioushawk
 * Text Domain:     movie-library
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @version
 */

namespace Movie_Library;

define( 'MOVIE_LIBRARY_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'MOVIE_LIBRARY_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

require_once __DIR__ . '/autloader.php';


use Widgets\Class_Movie_Widget\Movie_Widget;
use Widgets\Class_Person_Widget\Person_Widget;
use Includes\Class_Person_Rest_Api\Person_Rest_Api;
use Includes\Class_Movie_Rest_Api\Movie_Rest_Api;
require_once __DIR__ . '/includes/movie-library-helper.php';


use Includes\Class_Custom_Rewrite_Rules\Custom_Rewrite_Rules;
use Includes\Class_Movie_Media_Metabox\Movie_Media_Metabox;
use Includes\Class_Shortcode\Shortcode;
use Includes\Class_Settings_page\Settings_Page;
use Includes\Class_Rt_Person\Rt_Person;
use Includes\Class_Rt_Movie\Rt_Movie;
use Includes\Class_Custom_Table\Custom_Table;
use Includes\Class_Movie_Manager\Movie_Manager;
use Includes\Class_Dashboard_Widget\Dashboard_Widget;
use Includes\Class_Movie_Block_Render\Movie_Block_Render;
use Includes\Class_Person_Block_Render\Person_Block_Render;

/**
 * Main class for creating the all the functionality and creating all objects of supporting classes.
 */
class Movie_Library {
	/**
	 * Instantiate the class and create all the objects of the classes.
	 */
	public function __construct() {
		$rtm                 = new Rt_Movie();
		$rtp                 = new Rt_Person();
		$shortcode           = new Shortcode();
		$media_metabox       = new Movie_Media_Metabox();
		$settings            = new Settings_Page();
		$movie_rest_api      = new Movie_Rest_Api();
		$person_rest_api     = new Person_Rest_Api();
		$custom_table        = new Custom_Table();
		$rewrite             = new Custom_Rewrite_Rules();
		$dashboard_widgets   = new Dashboard_Widget();
		$movie_block_render  = new Movie_Block_Render();
		$person_block_render = new Person_Block_Render();

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once __DIR__ . '/includes/class-custom-wp-cli.php';
		}

		add_filter( 'enter_title_here', array( &$this, 'create_title_label' ) );
		add_filter( 'write_your_story', array( &$this, 'create_content_label' ) );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'widgets_init', array( $this, 'ml_register_widgets_sidebars' ) );
		add_action( 'plugins_loaded', array( $this, 'ml_custom_table_register' ) );
		register_activation_hook(
			__FILE__,
			array( $this, 'ml_function_on_activation' )
		);
		register_deactivation_hook(
			__FILE__,
			array( $this, 'ml_function_on_deactivation' )
		);
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_styles' ] );
	}

	/**
	 * Set the title label according to the type of post
	 *
	 * @param mixed $title the default title.
	 *
	 * @return string
	 */
	public function create_title_label( $title ) {
		$post = get_post_type();
		if ( 'rt-movie' === $post ) {
			$title = __( 'Movie Title', 'movie-library' );
		} elseif ( 'rt-person' === $post ) {
			$title = __( 'Name', 'movie-library' );
		}
		return $title;
	}

	/**
	 * Set the post content label for the type of post
	 *
	 * @param mixed $title the default title.
	 *
	 * @return string
	 */
	public function create_content_label( $title ) {
		$post = get_post_type();
		if ( 'rt-movie' === $post ) {
			$title = __( 'Plot', 'movie-library' );
		} elseif ( 'rt-person' === $post ) {
			$title = __( 'Biography', 'movie-library' );
		}
		return $title;
	}

	/**
	 * Enqueue the script to change the excerpt label if the post type is rt-movie
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		if ( 'rt-movie' === get_post_type() ) {
			wp_enqueue_script(
				'movie-library-admin',
				MOVIE_LIBRARY_URL . '/js/excerpt-label.js',
				[ 'wp-i18n' ],
				filemtime( MOVIE_LIBRARY_PATH . '/js/excerpt-label.js' ),
				true
			);
		}
	}

	/**
	 * Function to enqueue the stylesheets.
	 *
	 * @return void
	 */
	public function admin_enqueue_styles() {
		// Check if the current screen being displayed is admin dashboard or not.
		if ( 'dashboard' === get_current_screen()->id ) {
			wp_enqueue_style(
				'ml-dashboard-style',
				MOVIE_LIBRARY_URL . '/css/dashboard-style.css',
				array(),
				filemtime( MOVIE_LIBRARY_PATH . '/css/dashboard-style.css' )
			);
		}
	}
	/**
	 * Register sidebars for widgets.
	 *
	 * @return void
	 */
	public function ml_register_sidebars() {
		register_sidebar(
			array(
				'name'          => __( 'Movie Library Movies Sidebar', 'movie-library-theme' ),
				'id'            => 'ml-movies-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Movie Library Person Sidebar', 'movie-library-theme' ),
				'id'            => 'ml-person-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
	/**
	 * Register sidebars and widgets.
	 *
	 * @return void
	 */
	public function ml_register_widgets_sidebars() {

		register_sidebar(
			array(
				'name'          => __( 'Movie Library Movies Sidebar', 'movie-library-theme' ),
				'id'            => 'ml-movies-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Movie Library Person Sidebar', 'movie-library-theme' ),
				'id'            => 'ml-person-sidebar',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		$movie_widget  = new Movie_Widget();
		$person_widget = new Person_Widget();
		register_widget( $movie_widget );
		register_widget( $person_widget );
	}
	/**
	 * Register the custom wp_moviemeta and wp_personmeta tables.
	 *
	 * @return void
	 */
	public function ml_custom_table_register() {
		global $wpdb;
		$wpdb->moviemeta  = $wpdb->prefix . 'moviemeta';
		$wpdb->personmeta = $wpdb->prefix . 'personmeta';
	}

	/**
	 * Register all custom post types and taxonomies on activation so that role has access to it.
	 *
	 * @return void
	 */
	public function ml_function_on_activation() {
		$rtmovie = new Rt_Movie();
		$rtmovie->rt_movie_init();
		$rtmovie->create_genre_taxonomy();
		$rtmovie->create_label_taxonomy();
		$rtmovie->create_language_taxonomy();
		$rtmovie->create_movie_tags_taxonomy();
		$rtmovie->create_shadow_movie_person_taxonomy();

		$rtperson = new Rt_Person();
		$rtperson->rt_person_init();
		$rtperson->create_person_career_taxonomy();

		$custom_table = new Custom_Table();
		$custom_table->ml_create_custom_table();

		$movie_manager = new Movie_Manager();

		$rewrite = new Custom_Rewrite_Rules();
		$rewrite->add_custom_rewrite_rules();
		flush_rewrite_rules();
	}
	/**
	 * Remove roles on deactivation hook.
	 *
	 * @return void
	 */
	public function ml_function_on_deactivation() {
		$movie_manager = new Movie_Manager();
		$movie_manager->ml_remove_roles();
	}
}
$mlb = new Movie_Library();
