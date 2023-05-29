<?php
/**
 * Template part for displaying the search form to search on your blog.
 *
 * @package weston-theme
 */

?>
<div class="search-form-container">
	<?php get_search_form(); ?>
	<img src="<?php echo esc_url( STYLESHEET_DIR_URI . '/assets/svgs/close.svg' ); ?>" alt="<?php echo esc_attr_e( 'close search', 'movie-library' ); ?>" class="close-search">
</div>
