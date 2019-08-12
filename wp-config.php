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
define( 'DB_NAME', 'burh4753_wp781' );

/** MySQL database username */
define( 'DB_USER', 'burh4753_wp781' );

/** MySQL database password */
define( 'DB_PASSWORD', '8.t3lpSD3.' );

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
define( 'AUTH_KEY',         'tezhaqn9lakdfzqfpprnhz3w6bb7psapkojkjkdyhzzodyvi987dxls22vv4oz4i' );
define( 'SECURE_AUTH_KEY',  'x0r8c8hw7ypqg1uxxd9hcvruevrqu9qxpydfwsuiaf3ikleyzkiekhuxohmbyru5' );
define( 'LOGGED_IN_KEY',    'lcyenye48htbojmujdhspyegq3ge9xbngpx2jelhdrtpgd1sf3jxsbzipyd4nhgs' );
define( 'NONCE_KEY',        'xqxflzea65xuibhdf5xj0p1c6bxxuf5lp3sj8xmamtodpphohuuti6tywihthzcx' );
define( 'AUTH_SALT',        'fbaz3ggyd4kqcyyrbcjs2ybvoeedracjbjkljcitqtctesdz0sswkqbe36gs5gmb' );
define( 'SECURE_AUTH_SALT', 'dlml8z64jzjcuzogewypibodsh1x0keniapstl8xieqmzlbzkx3jnrssqv1xdkho' );
define( 'LOGGED_IN_SALT',   '1oaahnmjhx2v06atty2fd2ezyomt27iti25sjkmxuocpar4todw6wm1vikz1jmm8' );
define( 'NONCE_SALT',       'bgoj9bjqve4ywh0p8whgns4no9pbxr5erbrxvxvasahqwcd2fasqpbn55ovjgmjt' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wppn_';

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
