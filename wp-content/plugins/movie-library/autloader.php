<?php
/**
 * Create the autoloader for the project.
 *
 * @package movie-library
 */

spl_autoload_register( 'movie_library_autoloader' );

/**
 * Create the autoloader function registered with spl_autoloader_register()
 *
 * @param string $class The class name.
 * @return void
 */
function movie_library_autoloader( $class ) {
	$class = substr( $class, 0, strrpos( $class, '\\' ) );
	$class = str_replace( [ '\\', '_' ], [ '/', '-' ], $class );
	$class = strtolower( $class );
	$class = __DIR__ . '/' . $class . '.php';
	if ( ( validate_file( $class ) === 0 ) && file_exists( $class ) ) {
		require_once $class;
	}
}
