<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'techodsc_jobson' );

/** MySQL database username */
define( 'DB_USER', 'techodsc_jobson' );

/** MySQL database password */
define( 'DB_PASSWORD', 'z86Si@p)W2' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'e6fpqzidtzy2y2bhkwrniurym8qi7rnd36jotnwxbw8f9cd76n260zhweaholcef' );
define( 'SECURE_AUTH_KEY',  'jnzwcekvbfqn9fwc3xvlpu4ssg5yf8jtiaupsbmcjcmxs20oatnjwgxvaw0czi3w' );
define( 'LOGGED_IN_KEY',    'bm6isoihb8wfwnbo2qneh4bgpp2jn7xutcjns6nlselsc7be3ohzntbtxcrugez2' );
define( 'NONCE_KEY',        'rigu9bqlhaohqpb6eef8qxuz6pxiwsz3h1fgyagt4iv7el8wh5wwut8rjjjjqkqw' );
define( 'AUTH_SALT',        'g0vnyvwzsg98utrzjdk2izrsiochwmh4tt3nmnuu6mgjbimvcv7ld4ffadnqpd9r' );
define( 'SECURE_AUTH_SALT', 'pw9xtghikkvvle1m8ctev3yg68zzv1lpjpgvr9e0jhrbrhp2u0vxcd3gru2vrj2b' );
define( 'LOGGED_IN_SALT',   'pv508km7ekpobilviicwug4jthy0clbqb66hm3b7b20uibruu0bh0gjzm4oxon3v' );
define( 'NONCE_SALT',       'drspwn2jwcigk3awkecuzkit1lqat2dzkxdo52u3nsc9v10glwpcnbtxeecodzej' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wphr_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
