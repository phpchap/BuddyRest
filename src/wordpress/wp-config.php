<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'buddyrest');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '[2Ks+TBUArex.uD-Kj--WJB4Qc-&9g-9zme3cL~Fb, -[~+;53E@.0Y|qL|o-qdA');
define('SECURE_AUTH_KEY',  'wpT++vKN{10[XHS/+4@vlh<%S$1Y;r?+53-9lUk%3Cwrl3.z#^~(Y-X8xW}16Mn|');
define('LOGGED_IN_KEY',    'TD2l{V@}1ZU.5t@0B=<7D/yAJ;]+GOYx4Ew!|uN[+9^6gGL>+lZEFZ-P-3/njPi&');
define('NONCE_KEY',        'h~*{|c1N}q[.&$=j!7$yI?~[G$oV4;,<pQUEfc/Q=9 Jdl[st#_3;q!=qx{-#>.R');
define('AUTH_SALT',        '/i1<N]aR@Y|l`QG{S$DSp,e*60aYi}veb z1XJB-l_OOaz4k-Qy8s>EI!-LzXszG');
define('SECURE_AUTH_SALT', ',+m<=@BOA[@CAx~|->j@~+M;-_`/-}f7f0X$tmNjR;~4yUoir|+ayeKX&<<!B:hS');
define('LOGGED_IN_SALT',   's=(Em`l_AbE|YgUA+,z@V=[SXo(-n~;b~EV(Lsex*n%py$-;zgpwC Q.&rk+|j,x');
define('NONCE_SALT',       'LV2@BgjM?aAd/YTHnmm<wKTI3s4yYYL:NrDz -rW>p:fy1N++=y(|aPo!M/?f[!9');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
