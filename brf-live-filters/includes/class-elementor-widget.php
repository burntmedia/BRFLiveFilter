<?php
/**
 * Elementor widget integration.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Elementor_Widget {
    public static function register_widget( $widgets_manager ) {
        if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
            return;
        }

        require_once BRF_LF_PATH . 'includes/class-elementor-widget-instance.php';
        $widgets_manager->register( new BRF_LF_Elementor_Widget_Instance() );
    }
}
