<?php
/**
 * Plugin Name:       Custom Gutenberg
 * Description:       Plugin to create custom gutenberg block type Custom Post.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Piyush Tekwani
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-gutenberg
 *
 * @package           custom-gutenberg
 */

define( 'CUSTOM_POST_SLUG', 'cg-custom-post' );
define( 'BUILD_FOLDER', __DIR__ . '/blocks/build' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_custom_gutenberg_block_init() {
	register_block_type(
		BUILD_FOLDER . '/custom-gutenberg-block',
	);
}

/**
 * Create and register Custom Posts post-type.
 *
 * @return void
 */
function custom_post_type_init() {
	$labels = array(
		'name'               => __( 'Custom Posts', 'custom-gutenberg' ),
		'singular_name'      => __( 'Custom Post', 'custom-gutenberg' ),
		'menu_name'          => __( 'Custom Posts', 'custom-gutenberg' ),
		'name_admin_bar'     => __( 'Custom Post', 'custom-gutenberg' ),
		'add_new'            => __( 'Add New', 'custom-gutenberg' ),
		'add_new_item'       => __( 'Add New Custom Post', 'custom-gutenberg' ),
		'new_item'           => __( 'New Custom Post', 'custom-gutenberg' ),
		'edit_item'          => __( 'Edit Custom Post', 'custom-gutenberg' ),
		'view_item'          => __( 'View Custom Post', 'custom-gutenberg' ),
		'all_items'          => __( 'All Custom Posts', 'custom-gutenberg' ),
		'search_items'       => __( 'Search Custom Posts', 'custom-gutenberg' ),
		'parent_item_colon'  => __( 'Parent Custom Posts:', 'custom-gutenberg' ),
		'not_found'          => __( 'No custom posts found.', 'custom-gutenberg' ),
		'not_found_in_trash' => __( 'No custom posts found in Trash.', 'custom-gutenberg' ),
	);
	register_post_type(
		'cg-custom-post',
		[
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments', 'custom-fields' ],
			'has_archive'       => true,
			'query_var'         => true,
			'menu_position'     => null,
			'menu_icon'         => 'dashicons-admin-generic',
			'show_in_rest'      => true,
			'rest_base'         => CUSTOM_POST_SLUG,
		]
	);
}
/**
 * Disable gutenberg block editor for all other post types except cg-custom-post
 *
 * @param bool   $can_edit Whether or not we can edit it using gutenberg or not.
 * @param string $post_type The post type.
 * @return bool
 */
function enable_gutenberg_editor_for_post_type( $can_edit, $post_type ) {
	if ( CUSTOM_POST_SLUG === $post_type ) {
		return true;
	}

	// Disable Gutenberg editor for all other post types.
	return false;
}
// Hook function to disable block editor for other post types.
add_filter( 'use_block_editor_for_post_type', 'enable_gutenberg_editor_for_post_type', 10, 2 );
// Hook function to regsiter a block type.
add_action( 'init', 'create_block_custom_gutenberg_block_init' );
// Hook function to register a custom post type.
add_action( 'init', 'custom_post_type_init' );
