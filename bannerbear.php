<?php
/**
 * Plugin Name: Bannerbear
 * Plugin URI: https://github.com/BannerbearHQ/wp-bannerbear
 * Description: Generate dynamic banners, featured images, OG meta images, etc for your website.
 * Version: 1.0.0
 * Author: Bannerbear
 * Author URI: https://bannerbear.com
 * Text Domain: bannerbear
 * Domain Path: /languages/
 * Requires at least: 5.5
 * Requires PHP: 7.0
 *
 */

defined( 'ABSPATH' ) || exit;

// Define constants.
if ( ! defined( 'BANNERBEAR_PLUGIN_FILE' ) ) {
	define( 'BANNERBEAR_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'BANNERBEAR_VERSION' ) ) {
	define( 'BANNERBEAR_VERSION', '1.0.0' );
}

if ( ! defined( 'BANNERBEAR_DB_VERSION' ) ) {
	define( 'BANNERBEAR_DB_VERSION', 1 );
}

// Include the auto loader.
require 'vendor/autoload.php';

/**
 * Returns the main plugin instance.
 *
 * @since  1.0.0
 * @return Bannerbear\WP\Plugin
 */
function bannerbear() {
	return Bannerbear\WP\Plugin::instance();
}

// Kickstart the plugin.
bannerbear();
