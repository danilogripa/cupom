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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'ubuntu' );

/** MySQL database password */
define( 'DB_PASSWORD', '12345678' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


define('FS_METHOD', 'direct');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         '$T%pte{)y><2*)e,h(kPxmT1?ajgB@`ZrSFZM,-PMMUp~/g+<00HS/v`6d|{A6JK');
define('SECURE_AUTH_KEY',  '[})aPeR1}iXV@3ZRvw*8t5%.wYV#vU|hh=i6).`7OQgr_gZ33d~+c52+-<,%y$~w');
define('LOGGED_IN_KEY',    'yr^aP:+<A_VZHr3[tu1((w@K_7@&A-Hbo-/s5E]!+-MvL+_S_j@u-M`V~fE)2,I|');
define('NONCE_KEY',        '3?^z~GWJ4m]?;c:[`Ed.`qv3F6yb?6!f8TpQzB^yRBj)c@-wZ+ylPL Zq6Byi q:');
define('AUTH_SALT',        'U%8oDaWL@?k;dMQg1[p*|-$6E_;,;74E]ysnr >[PjGdFx/oa^r*rtOe O7#XsK{');
define('SECURE_AUTH_SALT', 'zH=2|>HFb,6(KIzvCkPB8|qfYb0455j--,M,?kK>vV!}$&YT$G?}(8j6S29ZzkA:');
define('LOGGED_IN_SALT',   '34Fsh-5;h?{CWlI7!9?BI5|hx_mKQ6v^p!Ni./)J,fkVniqVV-fh<|g?m}(jw<CY');
define('NONCE_SALT',       'g5e(6lf*,|EzAk&b03m{n&RQ?l]!.Pm2x|]<{%R_ZJ{+y7S)LS&zgE3<|IgX6$q7');




/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
@ini_set('upload_max_size' , '256M' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
