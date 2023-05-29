<?php
/**
 * Functions.php file for the child theme to add functionality.
 *
 * @package movie-library-theme
 */

define( 'STYLESHEET_DIR_URI', get_stylesheet_directory_uri() );
define( 'STYLESHEET_DIR', get_stylesheet_directory() );

require_once __DIR__ . '/classes/class-movie-library-customizer.php';


if ( ! function_exists( 'ml_enqueue_style' ) ) {
	/**
	 * Enqueue all the common styles and the styles specific to each post page type.
	 *
	 * @return void
	 */
	function ml_enqueue_style() {

		wp_register_style(
			'ml_style',
			get_stylesheet_uri(),
			array(),
			filemtime( STYLESHEET_DIR . '/style.css' ),
		);
		wp_enqueue_style( 'ml_style' );

		wp_register_style(
			'ml_header',
			STYLESHEET_DIR_URI . '/assets/css/header.css',
			array(),
			filemtime( STYLESHEET_DIR . '/assets/css/header.css' )
		);
		wp_enqueue_style( 'ml_header' );

		// CSS for customizer theme mods.

		$combined_css = '';

		$bg_color         = esc_attr( get_theme_mod( 'ml_body_background_color' ) );
		$custom_color_css = "
		.body-style {
			background-color: {$bg_color};
		}
		";
		$combined_css    .= $custom_color_css;

		$featured_image_height = esc_attr( get_theme_mod( 'ml_change_image_height' ) );

		$image_height_css = "
			.hero-movie .left-item img,
			.main-person .person-image img  {
			max-height: {$featured_image_height}px !important;
			object-fit: contain;
		}
		";

		$combined_css .= $image_height_css;

		$featured_image_width = esc_attr( get_theme_mod( 'ml_change_image_width' ) );
		$image_width_css      = "
		.hero-movie .left-item img,
		.main-person .person-image img   {
			max-width: {$featured_image_width}px !important;
			min-width: {$featured_image_width}px !important;
		}
		";
		$combined_css        .= $image_width_css;

		$content_separator = esc_attr( get_theme_mod( 'ml_separator' ) );
		if ( 'hyphen' === $content_separator ) {
			$custom_separator_css = '
			.hero-movie .right-item .movie-stats .divider,
			.hero-movie .right-item .movie-directors .divider {
				height: 1px !important;
				width: 5px !important;
				border-radius: 50%;
			}
			';
			$combined_css        .= $custom_separator_css;
		}

		if ( get_theme_mod( 'ml_change_sidebar_width' ) || get_theme_mod( 'ml_set_sidebar_width_unit' ) ) {
			$sidebar_width      = esc_attr( get_theme_mod( 'ml_change_sidebar_width' ) );
			$sidebar_width_unit = esc_attr( get_theme_mod( 'ml_set_sidebar_width_unit' ) );

			$custom_sidebar_width_css = "
			.movie-main-wrapper,
			.widget-person-wrapper,
			.movie-widget-wrapper,
			.movie-wrapper-header,
			.person-wrapper-header {
				width: {$sidebar_width}{$sidebar_width_unit};
				max-width: 400px !important;
				min-width: 200px !important;
			}
			";
			$combined_css            .= $custom_sidebar_width_css;
		}

		wp_add_inline_style( 'ml_header', $combined_css );

		wp_register_style(
			'ml_footer',
			STYLESHEET_DIR_URI . '/assets/css/footer.css',
			array(),
			filemtime( STYLESHEET_DIR . '/assets/css/footer.css' )
		);
		wp_enqueue_style( 'ml_footer' );

		if ( is_post_type_archive( 'rt-movie' ) ) {
			wp_register_style(
				'archive-rt-movie',
				STYLESHEET_DIR_URI . '/assets/css/archive-rt-movie.css',
				array(),
				filemtime(
					STYLESHEET_DIR . '/assets/css/archive-rt-movie.css'
				)
			);
			wp_enqueue_style( 'archive-rt-movie' );
		}

		if ( is_post_type_archive( 'rt-person' ) ) {
			wp_register_style(
				'archive-rt-person',
				STYLESHEET_DIR_URI . '/assets/css/archive-rt-person.css',
				array(),
				filemtime(
					STYLESHEET_DIR . '/assets/css/archive-rt-person.css'
				)
			);
			wp_enqueue_style( 'archive-rt-person' );
		}

		if ( is_singular( 'rt-movie' ) ) {
			wp_register_style(
				'single-rt-movie',
				STYLESHEET_DIR_URI . '/assets/css/single-rt-movie.css',
				array(),
				filemtime(
					STYLESHEET_DIR . '/assets/css/single-rt-movie.css'
				)
			);
			wp_enqueue_style( 'single-rt-movie' );
		}

		if ( is_singular( 'rt-person' ) ) {
			wp_register_style(
				'single-rt-person',
				STYLESHEET_DIR_URI . '/assets/css/single-rt-person.css',
				array(),
				filemtime(
					STYLESHEET_DIR . '/assets/css/single-rt-person.css'
				)
			);
			wp_enqueue_style( 'single-rt-person' );
		}
	}
}
if ( ! function_exists( 'ml_enqueue_scripts' ) ) {
	/**
	 * Enqueue the scripts for pages that require it.
	 *
	 * @return void
	 */
	function ml_enqueue_scripts() {

		wp_register_script( 'ml_slider', STYLESHEET_DIR_URI . '/assets/js/slider.js', array(), filemtime( STYLESHEET_DIR . '/assets/js/slider.js' ), true );
		wp_enqueue_script( 'ml_slider' );

		if ( is_post_type_archive( 'rt-movie' ) ) {
			wp_register_script( 'ml_loader', STYLESHEET_DIR_URI . '/assets/js/loader.js', array(), filemtime( STYLESHEET_DIR . '/assets/js/loader.js' ), true );
			wp_enqueue_script( 'ml_loader' );
		}

		if ( is_singular( 'rt-person' ) ) {
			wp_register_script( 'ml_loader', STYLESHEET_DIR_URI . '/assets/js/loader.js', array(), filemtime( STYLESHEET_DIR . '/assets/js/loader.js' ), true );
			wp_enqueue_script( 'ml_loader' );
		}

		wp_register_script( 'ml_search_bar', STYLESHEET_DIR_URI . '/assets/js/search-bar.js', array(), filemtime( STYLESHEET_DIR . '/assets/js/search-bar.js' ), true );
		wp_enqueue_script( 'ml_search_bar' );

	}
}
add_action( 'wp_enqueue_scripts', 'ml_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'ml_enqueue_scripts' );

/**
 * Register custom navigation menus to display inside the theme.
 *
 * @return void
 */
function register_theme_nav_menus() {
	register_nav_menus(
		array(
			'company_footer_menu' => __( 'Footer Company Nav', 'movie-library-theme' ),
			'explore_footer_menu' => __( 'Footer Explore Nav', 'movie-library-theme' ),
			'quick_links_menu'    => __( 'Quick Links Nav', 'movie-library-theme' ),
			'header_bottom_menu'  => __( 'Header Bottom Nav Menu', 'movie-library' ),
			'mobile_menu'         => __( 'Mobile Nav Menu', 'movie-library' ),
		)
	);
}

add_action( 'after_setup_theme', 'register_theme_nav_menus' );

if ( ! function_exists( 'ml_enqueue_customizer' ) ) {
	/**
	 * Enqueue the customizer.
	 *
	 * @return void
	 */
	function ml_enqueue_customizer() {
		$customizer = new Movie_Library_Customizer();
	}
}

ml_enqueue_customizer();
