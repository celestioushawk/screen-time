<?php
/**
 * Single page for the rt-movie type to didplay and call the template parts necessary for this page to run.
 *
 * @package movie-library-theme
 */

get_header();
get_template_part( 'template_parts/movie/movie-hero' );
get_template_part( 'template_parts/movie/movie-plot' );
get_template_part( 'template_parts/movie/movie-cast-and-crew' );
get_template_part( 'template_parts/movie/movie-snapshots' );
get_template_part( 'template_parts/movie/movie-clips' );
get_template_part( 'template_parts/movie/movie-reviews' );
if ( (bool) get_theme_mod( 'ml_single_posts_navigation' ) ) {
	get_template_part( 'template_parts/post-navigation' );
}
get_footer();
