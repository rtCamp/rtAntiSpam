<?php
/*
Plugin Name: AntiSpam
Description: Keep spammers out of your site using this plugin.
Version: 1.0
Author: rtcamp
Author URI: http://rtcamp.com
Tags: login, registration, ajax, antispam, anti-spam, register, widget, widgets
*/

/* Define Plugin Constants */
define( 'RTAS_BASENAME', plugin_basename(__FILE__) );
define( 'RTAS_DIR_PATH', plugin_dir_path(__FILE__) );
define( 'RTAS_DIR_URL', plugin_dir_url(__FILE__) );

/* Define Directory Path Constants */
define( 'RTAS_CSS', RTAS_DIR_PATH . 'css' );
define( 'RTAS_JS', RTAS_DIR_PATH . 'js' );
define( 'RTAS_IMG', RTAS_DIR_PATH . 'img' );

/* Define Directory URL Constants */
define( 'RTAS_CSS_DIR_URL', RTAS_DIR_URL . 'css' );
define( 'RTAS_JS_DIR_URL', RTAS_DIR_URL . 'js' );
define( 'RTAS_IMG_DIR_URL', RTAS_DIR_URL . 'img' );

/* Define common URL's to be used */
define( 'RTAS_LOGIN_URL', wp_login_url() );
define( 'RTAS_REGISTER_URL', site_url( 'wp-login.php?action=register', 'login_post' ) );
define( 'RTAS_LOSTPSWD_URL', wp_lostpassword_url() );

register_activation_hook( __FILE__, 'rt_anti_spam_defaults' );

$rt_anti_spam_settings = ( function_exists( 'is_multisite' ) && is_multisite() ) ? get_site_option( 'rt_anti_spam_settings' ) : get_option( 'rt_anti_spam_settings' );

/* Define Public & Private Keys */
define( 'RTAS_PUBLIC_KEY', $rt_anti_spam_settings['recaptcha']['public_key'] );
define( 'RTAS_PRIVATE_KEY', $rt_anti_spam_settings['recaptcha']['private_key'] );

/* Includes PHP files located in 'libs' folder */
foreach ( glob( RTAS_DIR_PATH . "php/libs/*.php" ) as $lib_filename ) {
    require_once( $lib_filename );
}

/* Includes PHP files located in 'php' folder */
foreach ( glob( RTAS_DIR_PATH . "php/*.php" ) as $php_filename ) {
    require_once( $php_filename );
}
