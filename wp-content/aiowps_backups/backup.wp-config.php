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

/**
 * Database connection information is automatically provided.
 * There is no need to set or change the following database configuration
 * values:
 *   DB_HOST
 *   DB_NAME
 *   DB_USER
 *   DB_PASSWORD
 *   DB_CHARSET
 *   DB_COLLATE
 */

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         '{%p!5$5cTW,;z3UOFNbMD$#0Dc#J:pdO.w;Iaap#7]u8U*R:k2L4<nu|XdxMxLw~');
define('SECURE_AUTH_KEY',  'vx;=qWSq0R29%!wIhZ(=6aaH6Zo?=OXpq2|Dmj@hr;^BY7bWy98U1qR2)L^5[ROj');
define('LOGGED_IN_KEY',    '1!f-FQ)v$DfMMICf#zQ{jP[r!tv{_AsVF$X4cV4;n,b)4|Qi{W9y=UVCE:*t],1L');
define('NONCE_KEY',        ']-^k<Zkqi0mrq.s>PMBgF>r?BZ<K5J0Z_poP!R%?cW--D2(ED~#!<)THnama,+8s');
define('AUTH_SALT',        '8%RD%?@h>)$p=8-X!80^:[$UU3#ZdDm.bu9QfA_ZV9KTcvMvILJ)kK<*afLk*z49');
define('SECURE_AUTH_SALT', 'YaR)[_[?xP5F3Xy*!dI2%ARGiHVn};0thh?Kx+A<3|[sphO.ta5b7F#a<<.9yn8-');
define('LOGGED_IN_SALT',   '^pvy$YFA074~B6x+ei?(;fAtP23Ma[JehFP.?DGF}5*$quUASf.D3D89uh^Hgozb');
define('NONCE_SALT',       'V9.+.p|QDqT9d@{,f$7ZshiWsu}w[h=@O#i[*uPO%Qh$0V68!?@4]9}{jqLezw3f');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'fqsi_';

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
if ( ! defined( 'WP_DEBUG') ) {
	define('WP_DEBUG', false);
}


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
