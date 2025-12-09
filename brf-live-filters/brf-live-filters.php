<?php
/**
 * Plugin Name: BRF Live Filters
 * Description: Flexible, fast, and modern live filtering for posts, pages, and custom post types.
 * Version: 1.0.0
 * Author: OpenAI
 * Text Domain: brf-live-filters
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

if ( ! defined( 'BRF_LF_PATH' ) ) {
define( 'BRF_LF_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BRF_LF_URL' ) ) {
define( 'BRF_LF_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BRF_LF_VERSION' ) ) {
define( 'BRF_LF_VERSION', '1.0.0' );
}

require_once BRF_LF_PATH . 'includes/class-loader.php';
require_once BRF_LF_PATH . 'includes/helpers.php';

BRF_LF_Loader::init();
