<?php
/**
 * Helper functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function brf_lf_get_cache_prefix() {
    $prefix = get_option( 'brf_lf_cache_prefix' );
    if ( empty( $prefix ) ) {
        $prefix = wp_generate_password( 8, false, false );
        update_option( 'brf_lf_cache_prefix', $prefix );
    }
    return $prefix;
}

function brf_lf_build_cache_key( $args ) {
    return 'brf_lf_' . brf_lf_get_cache_prefix() . '_' . md5( wp_json_encode( $args ) );
}

function brf_lf_purge_cache() {
    update_option( 'brf_lf_cache_prefix', wp_generate_password( 8, false, false ) );
}

function brf_lf_locate_template( $template ) {
    $paths = array(
        trailingslashit( get_stylesheet_directory() ) . 'brf-live-filters/' . $template,
        BRF_LF_PATH . 'templates/' . $template,
    );

    foreach ( $paths as $path ) {
        if ( file_exists( $path ) ) {
            return $path;
        }
    }

    return false;
}

function brf_lf_sanitize_filters( $filters ) {
    $clean = array();
    if ( empty( $filters ) || ! is_array( $filters ) ) {
        return $clean;
    }

    foreach ( $filters as $filter ) {
        $type = isset( $filter['type'] ) ? sanitize_key( $filter['type'] ) : '';
        $key  = isset( $filter['key'] ) ? sanitize_key( $filter['key'] ) : '';
        if ( empty( $type ) || empty( $key ) ) {
            continue;
        }

        $item = array(
            'type'  => $type,
            'key'   => $key,
            'label' => isset( $filter['label'] ) ? sanitize_text_field( $filter['label'] ) : ucfirst( $key ),
        );

        if ( 'meta_select' === $type && ! empty( $filter['choices'] ) ) {
            $choices           = array_map( 'sanitize_text_field', (array) $filter['choices'] );
            $item['choices']   = array_combine( $choices, $choices );
        }

        $clean[] = $item;
    }

    return $clean;
}
