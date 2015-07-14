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
define('DB_NAME', 'twis');

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
define('AUTH_KEY',         'n-*mD9]+_E%d;I@}XTv#nSPw{CB;<l``-F!_HAa25Dq3.$~uZp(o3$c8^ffhN/xF');
define('SECURE_AUTH_KEY',  ',8%R.9Gz}?>6H+ vyR$S/}`LN7h<Rnzvct,!{ZqNn9psLx$~H!(j:)gW#M01*F3y');
define('LOGGED_IN_KEY',    '?;J@!{vJXI28<se~zoj_[z!Y]SdB;bdJxN(ckx*IY.gp}H#8x{MLv-$1u=iO7rPY');
define('NONCE_KEY',        '*33d,G![dA8J5axSC}cV3maUK?*._11$m=3VaPZ|u/jq3?8D_cQCs-6U#S]9LB~=');
define('AUTH_SALT',        '0nL6dQSfo(~j4.UE$YFhHX):3)5SHZSP-bhzrytR&Hwco_!Zjb[PH62>@eAT`TGY');
define('SECURE_AUTH_SALT', '+=:QHpG=F<0&]Jg8HSci1k%.vNvV#7Z09F>?]J*c#H`?#Iy}%y?5f@L8v=d%^$a4');
define('LOGGED_IN_SALT',   'D@kyt5}#zM!dqmavnEKIyGJs J1<s&EftLg bTCa>g8!qG_u.L}iB})q&FaqBbTX');
define('NONCE_SALT',       '`WwN64E0=a5`3mkI713msy|pFj8`-&fudAh{b=1/c@?tQ5k|@1|!_$G>4fM<^RC6');

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
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

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


// memos eway




/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
