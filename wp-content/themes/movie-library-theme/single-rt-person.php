<?php
/**
 * Single page for the rt-person post type to display a person post in the same along with css.
 *
 * @package movie-library-theme
 */

get_header();
get_template_part( 'template_parts/person/person-header' );
get_template_part( 'template_parts/person/person-about' );
get_template_part( 'template_parts/person/person-popular-movies' );
get_template_part( 'template_parts/movie/movie-snapshots' );
get_template_part( 'template_parts/movie/movie-clips' );
if ( (bool) get_theme_mod( 'ml_single_posts_navigation' ) ) {
	get_template_part( 'template_parts/post-navigation' );
}
get_footer();
