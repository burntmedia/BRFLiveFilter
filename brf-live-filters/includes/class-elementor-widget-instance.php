<?php
/**
 * Elementor widget instance.
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Elementor_Widget_Instance extends Widget_Base {
    public function get_name() {
        return 'brf-live-filter';
    }

    public function get_title() {
        return __( 'BRF Live Filter', 'brf-live-filters' );
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return array( 'general' );
    }

    protected function register_controls() {
        $filter_sets = get_posts(
            array(
                'post_type'      => 'brf_filter_set',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            )
        );

        $options = array();
        foreach ( $filter_sets as $set ) {
            $options[ $set->ID ] = $set->post_title;
        }

        $this->start_controls_section(
            'content_section',
            array(
                'label' => __( 'Content', 'brf-live-filters' ),
            )
        );

        $this->add_control(
            'filter_id',
            array(
                'label'   => __( 'Filter Set', 'brf-live-filters' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $options,
            )
        );

        $this->add_control(
            'template',
            array(
                'label'   => __( 'Template', 'brf-live-filters' ),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'default' => __( 'Default (use filter layout)', 'brf-live-filters' ),
                    'grid'    => __( 'Grid', 'brf-live-filters' ),
                    'list'    => __( 'List', 'brf-live-filters' ),
                ),
                'default' => 'default',
            )
        );

        $this->add_control(
            'wrapper_class',
            array(
                'label'       => __( 'Wrapper CSS Class', 'brf-live-filters' ),
                'type'        => Controls_Manager::TEXT,
                'description' => __( 'Optional custom class added to the outer container.', 'brf-live-filters' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if ( empty( $settings['filter_id'] ) ) {
            echo '<div>' . esc_html__( 'Select a filter set to preview.', 'brf-live-filters' ) . '</div>';
            return;
        }

        $shortcode = '[brf_live_filters id="' . absint( $settings['filter_id'] ) . '"]';
        $output    = do_shortcode( $shortcode );
        if ( 'grid' === $settings['template'] || 'list' === $settings['template'] ) {
            $output = str_replace( 'data-layout="default"', 'data-layout="' . esc_attr( $settings['template'] ) . '"', $output );
        }

        if ( ! empty( $settings['wrapper_class'] ) ) {
            $output = str_replace( 'brf-lf-wrapper"', 'brf-lf-wrapper ' . esc_attr( $settings['wrapper_class'] ) . '"', $output );
        }

        echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
