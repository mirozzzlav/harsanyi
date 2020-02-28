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
define( 'DB_NAME', 'sloncomp_harsanyi' );

/** MySQL database username */
define( 'DB_USER', 'sloncomp_prjcts' );

/** MySQL database password */
define( 'DB_PASSWORD', 'projects777^' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', 'utf8_general_ci' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'B=*3wNy+4 NpSewJ:ZD0WbvhZp-dF+@a9@@(hsM!;-r*&Wf-&}{z_Y82;Teiq2hP' );
define( 'SECURE_AUTH_KEY',  'CIIO*%%=Y+G.]a];T~}(_n*lw?-A!KJ2]C2zX<pVzAOMNy!$jyVhp$J%ORDtXjjP' );
define( 'LOGGED_IN_KEY',    'UG2K.rd=IOb):KK3qfKS@?yP(S%]fhLyVr] :y7QG6NRs9V`R w|W},@p(mKOX0Z' );
define( 'NONCE_KEY',        '9tHsR,:z?<^o)74<@$`!,a#ith12e,HEnOea](s{jTeRi6xfr?XiJ&0V;BWK:%V8' );
define( 'AUTH_SALT',        'aqRCTBSd!+=UlU;i#wT[Bkz+XO=D4YK{$n=7PSQ:oEYb0z/64b2LP:e$d*=s]q@]' );
define( 'SECURE_AUTH_SALT', '1z)F4]jJ&z1s>YN%4NU.:`2VX3RVjKN^SgtCrrwB*@FjmdA2H7LipbA~_V~2ac+`' );
define( 'LOGGED_IN_SALT',   'n@wp[Dc#d^m4~8@WWseJL^tvpo}0cu9#ep_3:oq pte{%}[3zEziP2$!`Ff%jsRF' );
define( 'NONCE_SALT',       'x7T>*qT;?oS7AxFi$OcXKWola;F{587RG|3<2//#`v$8t;di+G&c<|4L7{=uA7o2' );

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

