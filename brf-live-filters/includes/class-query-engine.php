<?php
/**
 * Query engine for BRF Live Filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Query_Engine {
    public static function build_query_args( $settings, $params = array() ) {
        $args = array(
            'post_type'           => isset( $settings['post_type'] ) ? sanitize_key( $settings['post_type'] ) : 'post',
            'posts_per_page'      => isset( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 6,
            'orderby'             => isset( $params['orderby'] ) ? sanitize_key( $params['orderby'] ) : ( isset( $settings['orderby'] ) ? sanitize_key( $settings['orderby'] ) : 'date' ),
            'order'               => isset( $params['order'] ) ? sanitize_text_field( $params['order'] ) : ( isset( $settings['order'] ) ? sanitize_text_field( $settings['order'] ) : 'DESC' ),
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'paged'               => isset( $params['page'] ) ? absint( $params['page'] ) : 1,
            's'                   => isset( $params['search'] ) ? sanitize_text_field( $params['search'] ) : '',
        );

        $tax_query = array();
        if ( ! empty( $params['taxonomies'] ) && is_array( $params['taxonomies'] ) ) {
            foreach ( $params['taxonomies'] as $taxonomy => $terms ) {
                $term_ids = array_map( 'absint', (array) $terms );
                if ( ! empty( $term_ids ) ) {
                    $tax_query[] = array(
                        'taxonomy' => sanitize_key( $taxonomy ),
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                    );
                }
            }
        }

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        $meta_query = array();
        if ( ! empty( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $key => $value ) {
                $clean_key = sanitize_key( $key );
                if ( '' === $value ) {
                    continue;
                }

                $meta_query[] = array(
                    'key'     => $clean_key,
                    'value'   => $value,
                    'compare' => 'LIKE',
                );
            }
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        return $args;
    }

    public static function query( $settings, $params = array() ) {
        $args     = self::build_query_args( $settings, $params );
        $cache_id = brf_lf_build_cache_key( $args );
        $cached   = get_transient( $cache_id );

        if ( false !== $cached ) {
            return $cached;
        }

        $query = new WP_Query( $args );
        set_transient( $cache_id, $query, HOUR_IN_SECONDS );
        return $query;
    }
}
