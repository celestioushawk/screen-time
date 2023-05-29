<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6P6R15MRu02X+g/7VOKH2kB+RistEQFZbIrkg/V4Ta+5kbTWbMrmQQiAej9FsxQm6WGkk3DaNQ0mU+ODWt3C9Q==');
define('SECURE_AUTH_KEY',  'CkqJrHgMLjco4SaHOaQ42rcveXNd/TjfBZG+K6HizcR2Q52uOzD9MncgOnTKLXUTvn/BO3D7OUI5gi5gu5WUng==');
define('LOGGED_IN_KEY',    'ZVAUVk5cRKWlB17CiAyou89NVAcEGJfXPtfCxIWVdwPGWjeYWB6Adt53UN+qCYTVk4H+Tp3hKRc8U3T66c8yVA==');
define('NONCE_KEY',        'RO32oacx+u6jmHSJacO3X1mTSV4LEqChE4uWuIrHRk73QhJDr1hYnZd9FpbwWz41otU0ZV/bK7BhcAZbe8OCxg==');
define('AUTH_SALT',        'UY+oVIVJbbQ+AdqBeZ55a6VARggTxEVb8paf05/pVc9BvcykkRrBCwraJuXz4xTGx+GjOUizMP2Ng7+Q9ihisg==');
define('SECURE_AUTH_SALT', 'tl/uccTKtIMowTQ2FsGoP0APvMHtwTbTG+G+QILg8l3MkdhFSe4KVVje834yxcfbtWiszswKPiJz6w0LE9YFSw==');
define('LOGGED_IN_SALT',   '4ULQZCbAY5hpDRdur+F3e+BvgKK2ZyXAKC6pLjyu8JdDLw1goKBvaXEXl4Sh0EoszTrvKqZukFHOpylRfz4gmw==');
define('NONCE_SALT',       'uAxq8bK0bCyjs8m1fnAPjuN/2Wy9Iwr77ONIs9hYOhQQlcngMJ9bP0+nT/EdiMoURqJDLok9J+wClmce9B+0dw==');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
