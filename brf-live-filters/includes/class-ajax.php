<?php
/**
 * AJAX endpoints for BRF Live Filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Query;

class BRF_LF_Ajax {
    public static function register() {
        add_action( 'wp_ajax_brf_live_filters', array( __CLASS__, 'handle' ) );
        add_action( 'wp_ajax_nopriv_brf_live_filters', array( __CLASS__, 'handle' ) );
    }

    public static function handle() {
        check_ajax_referer( 'brf_lf_nonce', 'nonce' );

        $filter_id = isset( $_POST['filterId'] ) ? absint( $_POST['filterId'] ) : 0;
        $settings  = BRF_LF_Shortcodes::get_settings( $filter_id );
        if ( empty( $settings ) ) {
            wp_send_json_error();
        }

        $params = array(
            'taxonomies' => array(),
            'meta'       => array(),
            'page'       => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
            'orderby'    => isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : '',
            'order'      => isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '',
        );

        if ( ! empty( $_POST['tax'] ) && is_array( $_POST['tax'] ) ) {
            foreach ( $_POST['tax'] as $tax => $terms ) {
                $params['taxonomies'][ sanitize_key( $tax ) ] = array_map( 'absint', (array) wp_unslash( $terms ) );
            }
        }

        if ( ! empty( $_POST['meta'] ) && is_array( $_POST['meta'] ) ) {
            foreach ( $_POST['meta'] as $key => $value ) {
                $params['meta'][ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( $value ) );
            }
        }

        $query = BRF_LF_Query_Engine::query( $settings, $params );
        ob_start();
        self::render_results( $query, $settings );
        $html = ob_get_clean();
        wp_send_json_success(
            array(
                'html'      => $html,
                'found'     => (int) $query->found_posts,
                'max_pages' => (int) $query->max_num_pages,
            )
        );
    }

    public static function render_results( WP_Query $query, $settings ) {
        $template = 'grid.php';
        if ( 'list' === $settings['layout'] ) {
            $template = 'list.php';
        } elseif ( 'custom' === $settings['layout'] && ! empty( $settings['custom_template'] ) ) {
            $template = $settings['custom_template'];
        }

        $located = brf_lf_locate_template( $template );
        if ( $located ) {
            include $located;
        }
    }
}
