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
define('DB_NAME', 'theescaperoom_narrativec');

/** MySQL database username */
define('DB_USER', 'theescaperoomnar');

/** MySQL database password */
define('DB_PASSWORD', '67Ud?e-d');

/** MySQL hostname */
define('DB_HOST', 'mysql.theescaperoom.narrativecard.com');

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
define('AUTH_KEY',         '#XMuFL%f`HOwG~bQW@wJ"I~D`L~Ko6)u8?)G9TNYU/i4QpA~SE_~yc9W3qU$OoC;');
define('SECURE_AUTH_KEY',  '9!GFK$83wxZDmeQHe+zuQ&mX:@Tai2B/@e#TtvGG)Euq!EhTbj3NLys@symXhkTA');
define('LOGGED_IN_KEY',    '8pzc$0LWixs;zr3N|*Y3:WPOwnkNKwkeyTN#rHeVWU`xJOG8p_QUcMT%^SY%!:U3');
define('NONCE_KEY',        'opw9!"R;oQS&Al^qJa8#lMM;zDqUAnRtW?NNET):)wN:4"S$LM^PVqW*UXe+B`SQ');
define('AUTH_SALT',        '?9S!28p`GS:X^5W%ayvKV46UE?7Xa5O@XIo?j3uA)@9%;mt*MjaewEOoy(?;ncU^');
define('SECURE_AUTH_SALT', '5z"_N8*+I0/Y?wK$pk/R~XI^dalld*mD2JoM/sH4TiM$W`WHKTu;scaQ1!A^Ae?c');
define('LOGGED_IN_SALT',   'M12qJB39;Z_@Q(Vkw?1J*@XlYn/NGg#c2/YeAmyiqR5p@Bw!L&nwyXou(rQcextB');
define('NONCE_SALT',       'xN)1iTopypJ4jWbeI&~2h`ApGPuOC!@#%sw*:Xi&B0s(8qGC;#7#Ds:noX::yqtd');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_uux6hp_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

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

