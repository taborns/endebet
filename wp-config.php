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
define( 'DB_NAME', 'endebet' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'strongpass' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         'jAM?+t/>QGlUbys2#~`^pU=VYPt;/-^DS-Osz=,jJKZfYy5nOHsMF9{pV ZjAB]z' );
define( 'SECURE_AUTH_KEY',  'T+=<v Y p7OP4.srAPs)1aBphKbMg3EXn_.t%)`a2]w)dmc:&7GB,t~`j;sNY%sO' );
define( 'LOGGED_IN_KEY',    'f#LBs+Hg<0eGP>sdh`t@},#<gzVI0/1F`%a;=u84sD7,TfK{)p^h;{OzUNcEjvju' );
define( 'NONCE_KEY',        'V;`G2id-uN+;Gt,>V]z0Dw&RSI*,9C-Jr8uwAlCGSgQySq(RM|C>:dwR^laE8Ybo' );
define( 'AUTH_SALT',        't!17<F(y$*.=;_vchS{IwFz,,_)kONrCR0^k}SEZZxioh+s Adob;bJ&8YNLdb5a' );
define( 'SECURE_AUTH_SALT', 'sQGiQS%/]2fg.oQCJXI/=qmvzGBqQq=jJJh!:lv,j/;Q22|dT6~dW7ip4hZVPI:f' );
define( 'LOGGED_IN_SALT',   ';4N(#EEGU,T|`,g-ib}KWe^8@.T|3j)o.<L)BU4Ul`5(V87:.NN`)!8.H;Uk{qw>' );
define( 'NONCE_SALT',       ',HZmKRjeB7^`+VlK*yQ[tz(7`GnX,2YVm1e@P`%yuVQ4?$gf+2dIIAmQ(W![[fd9' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'shola_';

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
