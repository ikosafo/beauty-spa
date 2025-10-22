<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'beauty-spa' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'EbBNiGt8v)Cw4o[y|6Vlgo!xd%EH>#;c]9w41_UEk?TG1jeR;Yg^g~2|syO6?*ek' );
define( 'SECURE_AUTH_KEY',  '/XlY(@+x1IpNIStS3wC^i/my$`1(oj8TKXz-^!F(`O;Win6dZeccl#beHm.b{K+9' );
define( 'LOGGED_IN_KEY',    '.~+iwZI|m8shUS/veZ*[SFh(uLP>6)3cjvv0QQn5Z=K7S#C^K#bGLf)8oxUC(1ss' );
define( 'NONCE_KEY',        'tb4Opmhl)x;z9w)m@]m!`o|}DGz|HeraW0P4cmsbGOja-=%]1 2m<AHCQ=<|2KmA' );
define( 'AUTH_SALT',        'PvZ6l?{iU/:2F>ZTSG)]L {Kf1gl?p7|^S-_slKQjC-u=(GS)xRKD||h`~-]>7>7' );
define( 'SECURE_AUTH_SALT', 'X9id22>pF%zmP+I?!al%WBy#34RT@r0Y$Nv:EKj;]81vM;~:d=fvOaSAuI<C&u3r' );
define( 'LOGGED_IN_SALT',   '~ Co*,1RUj5N^hp9*z55666!I+B3k<9|GmT3^@Ck4W83V@U2Bni`G_m%#A$XE^%A' );
define( 'NONCE_SALT',       '58|pjz08#gf@}Pg<2c)PvmZt)4SZ?WSm9vdJO9}*`C]=j~}s8w6Z4kmtz`ol_F|[' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
