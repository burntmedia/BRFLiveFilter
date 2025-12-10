<?php
/**
 * Front-end form template.
 *
 * @var array $settings
 */

$query = BRF_LF_Query_Engine::query( $settings );
$layout = ! empty( $settings['layout'] ) ? $settings['layout'] : 'grid';
?>
<div class="brf-lf-wrapper" data-filter-id="<?php echo esc_attr( $settings['id'] ); ?>" data-layout="<?php echo esc_attr( $layout ); ?>">
    <form class="brf-lf-form">
        <input type="hidden" name="action" value="brf_live_filters" />
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'brf_lf_nonce' ) ); ?>" />
        <input type="hidden" name="filterId" value="<?php echo esc_attr( $settings['id'] ); ?>" />
        <input type="hidden" name="page" value="1" />
        <div class="brf-lf-row">
            <div class="brf-lf-control brf-lf-search">
                <label for="brf-lf-search"><?php esc_html_e( 'Search', 'brf-live-filters' ); ?></label>
                <input
                    type="search"
                    id="brf-lf-search"
                    name="search"
                    placeholder="<?php esc_attr_e( 'Search all articles', 'brf-live-filters' ); ?>"
                />
            </div>

            <?php if ( ! empty( $settings['taxonomies'] ) ) : ?>
                <?php foreach ( $settings['taxonomies'] as $taxonomy ) : ?>
                    <?php
                    $tax_obj = get_taxonomy( $taxonomy );
                    if ( ! $tax_obj ) {
                        continue;
                    }

                    $terms = get_terms(
                        array(
                            'taxonomy'   => $taxonomy,
                            'hide_empty' => true,
                        )
                    );

                    if ( is_wp_error( $terms ) ) {
                        continue;
                    }
                    ?>
                    <div class="brf-lf-control">
                        <label for="brf-lf-tax-<?php echo esc_attr( $taxonomy ); ?>"><?php echo esc_html( $tax_obj->labels->name ); ?></label>
                        <select id="brf-lf-tax-<?php echo esc_attr( $taxonomy ); ?>" name="tax[<?php echo esc_attr( $taxonomy ); ?>]">
                            <option value=""><?php printf( esc_html__( 'All %s', 'brf-live-filters' ), esc_html( $tax_obj->labels->name ) ); ?></option>
                            <?php foreach ( $terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="brf-lf-control">
                <label for="brf-lf-orderby"><?php esc_html_e( 'Sort By', 'brf-live-filters' ); ?></label>
                <select id="brf-lf-orderby" name="orderby">
                    <option value="date" <?php selected( $settings['orderby'], 'date' ); ?>><?php esc_html_e( 'Date', 'brf-live-filters' ); ?></option>
                    <option value="title" <?php selected( $settings['orderby'], 'title' ); ?>><?php esc_html_e( 'Title', 'brf-live-filters' ); ?></option>
                    <option value="menu_order" <?php selected( $settings['orderby'], 'menu_order' ); ?>><?php esc_html_e( 'Manual Order', 'brf-live-filters' ); ?></option>
                </select>
            </div>
            <div class="brf-lf-control">
                <label for="brf-lf-order"><?php esc_html_e( 'Order', 'brf-live-filters' ); ?></label>
                <select id="brf-lf-order" name="order">
                    <option value="DESC" <?php selected( $settings['order'], 'DESC' ); ?>><?php esc_html_e( 'Descending', 'brf-live-filters' ); ?></option>
                    <option value="ASC" <?php selected( $settings['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'brf-live-filters' ); ?></option>
                </select>
            </div>
        </div>

        <?php if ( ! empty( $settings['meta_fields'] ) ) : ?>
            <div class="brf-lf-meta">
                <?php foreach ( $settings['meta_fields'] as $field ) : ?>
                    <div class="brf-lf-control">
                        <label for="meta-<?php echo esc_attr( $field['key'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                        <?php if ( 'meta_select' === $field['type'] ) : ?>
                            <select id="meta-<?php echo esc_attr( $field['key'] ); ?>" name="meta[<?php echo esc_attr( $field['key'] ); ?>]">
                                <option value=""><?php esc_html_e( 'Any', 'brf-live-filters' ); ?></option>
                                <?php foreach ( $field['choices'] as $choice_value => $choice_label ) : ?>
                                    <option value="<?php echo esc_attr( $choice_value ); ?>"><?php echo esc_html( $choice_label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ( 'meta_boolean' === $field['type'] ) : ?>
                            <label class="brf-lf-switch">
                                <input type="checkbox" id="meta-<?php echo esc_attr( $field['key'] ); ?>" name="meta[<?php echo esc_attr( $field['key'] ); ?>]" value="1" />
                                <span><?php esc_html_e( 'Yes', 'brf-live-filters' ); ?></span>
                            </label>
                        <?php else : ?>
                            <input type="text" id="meta-<?php echo esc_attr( $field['key'] ); ?>" name="meta[<?php echo esc_attr( $field['key'] ); ?>]" />
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="button brf-lf-submit"><?php esc_html_e( 'Apply Filters', 'brf-live-filters' ); ?></button>
    </form>

    <div class="brf-lf-results" aria-live="polite">
        <?php BRF_LF_Ajax::render_results( $query, $settings ); ?>
    </div>
</div>
