<?php
/**
 * Shortcodes for BRF Live Filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Shortcodes {
    public static function register() {
        add_shortcode( 'brf_live_filters', array( __CLASS__, 'render' ) );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'id' => 0,
                'post_type'      => '',
                'posts_per_page' => '',
                'orderby'        => '',
                'order'          => '',
                'layout'         => '',
                'template'       => '',
                'taxonomies'     => '',
                'meta_fields'    => '',
            ),
            $atts,
            'brf_live_filters'
        );

        $post_id = absint( $atts['id'] );
        if ( ! $post_id ) {
            return '';
        }

        $settings = self::get_settings( $post_id );
        if ( empty( $settings ) ) {
            return '';
        }

        $settings = self::merge_overrides( $settings, $atts );

        wp_enqueue_style( 'brf-lf-frontend' );
        wp_enqueue_script( 'brf-lf-frontend' );

        wp_localize_script(
            'brf-lf-frontend',
            'BRFLiveFilters',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'brf_lf_nonce' ),
            )
        );

        ob_start();
        include BRF_LF_PATH . 'templates/form.php';
        return ob_get_clean();
    }

    public static function get_settings( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post || 'brf_filter_set' !== $post->post_type ) {
            return array();
        }

        $meta_raw    = get_post_meta( $post_id, '_brf_lf_meta_fields', true );
        $meta_fields = self::parse_meta_fields( $meta_raw );

        $taxonomies = array();
        $tax_raw    = get_post_meta( $post_id, '_brf_lf_taxonomies', true );
        if ( $tax_raw ) {
            $taxonomies = array_map( 'sanitize_key', array_filter( array_map( 'trim', explode( ',', $tax_raw ) ) ) );
        }

        return array(
            'id'              => $post_id,
            'title'           => $post->post_title,
            'post_type'       => get_post_meta( $post_id, '_brf_lf_post_type', true ),
            'posts_per_page'  => (int) get_post_meta( $post_id, '_brf_lf_posts_per_page', true ),
            'orderby'         => get_post_meta( $post_id, '_brf_lf_orderby', true ),
            'order'           => get_post_meta( $post_id, '_brf_lf_order', true ),
            'layout'          => get_post_meta( $post_id, '_brf_lf_layout', true ),
            'custom_template' => get_post_meta( $post_id, '_brf_lf_custom_template', true ),
            'meta_fields'     => $meta_fields,
            'taxonomies'      => $taxonomies,
        );
    }

    protected static function parse_meta_fields( $raw ) {
        $fields = array();

        if ( $raw ) {
            $lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
            foreach ( $lines as $line ) {
                $parts = array_map( 'trim', explode( '|', $line ) );
                if ( count( $parts ) >= 3 ) {
                    $field = array(
                        'key'   => sanitize_key( $parts[0] ),
                        'type'  => sanitize_key( $parts[1] ),
                        'label' => sanitize_text_field( $parts[2] ),
                    );

                    if ( isset( $parts[3] ) && 'meta_select' === $field['type'] ) {
                        $choices          = array_map( 'sanitize_text_field', explode( ',', $parts[3] ) );
                        $field['choices'] = array_combine( $choices, $choices );
                    }

                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }

    protected static function merge_overrides( $settings, $atts ) {
        if ( ! empty( $atts['post_type'] ) ) {
            $settings['post_type'] = sanitize_key( $atts['post_type'] );
        }

        if ( '' !== $atts['posts_per_page'] ) {
            $settings['posts_per_page'] = (int) $atts['posts_per_page'];
        }

        if ( ! empty( $atts['orderby'] ) ) {
            $settings['orderby'] = sanitize_key( $atts['orderby'] );
        }

        if ( ! empty( $atts['order'] ) ) {
            $settings['order'] = sanitize_text_field( $atts['order'] );
        }

        if ( ! empty( $atts['layout'] ) ) {
            $settings['layout'] = sanitize_key( $atts['layout'] );
        }

        if ( ! empty( $atts['template'] ) ) {
            $settings['custom_template'] = sanitize_text_field( $atts['template'] );
        }

        if ( ! empty( $atts['taxonomies'] ) ) {
            $settings['taxonomies'] = array_map( 'sanitize_key', array_filter( array_map( 'trim', explode( ',', $atts['taxonomies'] ) ) ) );
        }

        if ( ! empty( $atts['meta_fields'] ) ) {
            $settings['meta_fields'] = self::parse_meta_fields( $atts['meta_fields'] );
        }

        return $settings;
    }
}
