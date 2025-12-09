<?php
/**
 * Loader for BRF Live Filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Loader {
    public static function init() {
        self::includes();
        self::hooks();
    }

    private static function includes() {
        require_once BRF_LF_PATH . 'includes/class-admin-ui.php';
        require_once BRF_LF_PATH . 'includes/class-shortcodes.php';
        require_once BRF_LF_PATH . 'includes/class-query-engine.php';
        require_once BRF_LF_PATH . 'includes/class-ajax.php';
        require_once BRF_LF_PATH . 'includes/class-elementor-widget.php';
    }

    private static function hooks() {
        add_action( 'init', array( 'BRF_LF_Admin_UI', 'register_post_type' ) );
        add_action( 'init', array( 'BRF_LF_Shortcodes', 'register' ) );
        add_action( 'init', array( 'BRF_LF_Ajax', 'register' ) );
        add_action( 'init', array( 'BRF_LF_Admin_UI', 'register_assets' ) );
        add_action( 'add_meta_boxes', array( 'BRF_LF_Admin_UI', 'add_meta_boxes' ) );
        add_action( 'save_post_brf_filter_set', array( 'BRF_LF_Admin_UI', 'save_meta' ) );
        add_action( 'save_post', 'brf_lf_purge_cache' );
        add_action( 'created_term', 'brf_lf_purge_cache' );
        add_action( 'edited_term', 'brf_lf_purge_cache' );
        add_action( 'delete_term', 'brf_lf_purge_cache' );
        add_action( 'elementor/widgets/register', array( 'BRF_LF_Elementor_Widget', 'register_widget' ) );
    }
}
