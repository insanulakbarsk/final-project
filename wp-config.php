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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'final_project' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '-oQ6^2ZG~IT?aYAx07@Y)_YCmYlD`R @42YE%]>w7i~QgbVrW^GgmV5.H}!W[ Ga' );
define( 'SECURE_AUTH_KEY',  '4>oqIr^u*?Kr,6#`ay33K?v(GM!LZ;JK 7lFubfT=?0LwM*vn<3qcNrYic0.*V~M' );
define( 'LOGGED_IN_KEY',    'R`%_B&,6uRE83&A6!n4A-Nm*~,a~quPo8HDb+-lpX@X<>;gWG#1{.c_?ks2PEdFO' );
define( 'NONCE_KEY',        'dBUjAjE[XK>ejB@{7T!`2I7j}H9HV]8C=7^;d*XKPGHYtrZX(B/*oEFL_siPsE<;' );
define( 'AUTH_SALT',        'I?;I@`O y/IpR}`K^W;^.RKpSM|kSAmdh5MjPV|SH1#z.q`p,YccAox{EolvYb5F' );
define( 'SECURE_AUTH_SALT', 'd{Dxq^i;!i(u{N1Mk]{mwpIX:wW_wl%!i:Jhl2f&0a;5b_!fA0n)v~fg_Fq3(,eg' );
define( 'LOGGED_IN_SALT',   '{f{k!KoxZO^F+bvf65l2?(p`i0r-Q]+|RG`P1j#6egy9C-l)n0?1B;UC$5)ca`DY' );
define( 'NONCE_SALT',       '7Yv*U|G:MT3B=]P4_k9DlU2wa7zV1WANpF:Dugh|#tIzm9@N?Y0:Id`VqI  @Zc;' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
