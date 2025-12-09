<?php
/**
 * Admin UI for BRF Live Filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRF_LF_Admin_UI {
    public static function register_post_type() {
        $labels = array(
            'name'               => __( 'Live Filters', 'brf-live-filters' ),
            'singular_name'      => __( 'Live Filter Set', 'brf-live-filters' ),
            'menu_name'          => __( 'Live Filters', 'brf-live-filters' ),
            'add_new'            => __( 'Add New', 'brf-live-filters' ),
            'add_new_item'       => __( 'Add New Filter Set', 'brf-live-filters' ),
            'edit_item'          => __( 'Edit Filter Set', 'brf-live-filters' ),
            'new_item'           => __( 'New Filter Set', 'brf-live-filters' ),
            'view_item'          => __( 'View Filter Set', 'brf-live-filters' ),
            'search_items'       => __( 'Search Filter Sets', 'brf-live-filters' ),
            'not_found'          => __( 'No filter sets found.', 'brf-live-filters' ),
            'not_found_in_trash' => __( 'No filter sets found in Trash.', 'brf-live-filters' ),
        );

        register_post_type(
            'brf_filter_set',
            array(
                'labels'          => $labels,
                'public'          => false,
                'show_ui'         => current_user_can( 'manage_options' ),
                'show_in_menu'    => current_user_can( 'manage_options' ),
                'capability_type' => 'post',
                'menu_position'   => 59,
                'menu_icon'       => 'dashicons-filter',
                'supports'        => array( 'title' ),
            )
        );
    }

    public static function register_assets() {
        wp_register_style( 'brf-lf-frontend', BRF_LF_URL . 'assets/frontend.css', array(), BRF_LF_VERSION );
        wp_register_script( 'brf-lf-frontend', BRF_LF_URL . 'assets/frontend.js', array(), BRF_LF_VERSION, true );
    }

    public static function add_meta_boxes() {
        add_meta_box( 'brf-lf-query', __( 'Query', 'brf-live-filters' ), array( __CLASS__, 'render_query_meta' ), 'brf_filter_set', 'normal', 'high' );
        add_meta_box( 'brf-lf-layout', __( 'Layout', 'brf-live-filters' ), array( __CLASS__, 'render_layout_meta' ), 'brf_filter_set', 'side', 'default' );
        add_meta_box( 'brf-lf-filters', __( 'Filters', 'brf-live-filters' ), array( __CLASS__, 'render_filters_meta' ), 'brf_filter_set', 'normal', 'default' );
    }

    public static function render_query_meta( $post ) {
        $post_type      = get_post_meta( $post->ID, '_brf_lf_post_type', true );
        $posts_per_page = (int) get_post_meta( $post->ID, '_brf_lf_posts_per_page', true );
        $orderby        = get_post_meta( $post->ID, '_brf_lf_orderby', true );
        $order          = get_post_meta( $post->ID, '_brf_lf_order', true );
        $nonce          = wp_create_nonce( 'brf_lf_meta_nonce' );

        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        ?>
        <input type="hidden" name="brf_lf_meta_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
        <p>
            <label for="brf_lf_post_type"><?php esc_html_e( 'Post Type', 'brf-live-filters' ); ?></label>
            <select id="brf_lf_post_type" name="brf_lf_post_type" class="widefat">
                <?php foreach ( $post_types as $type ) : ?>
                    <option value="<?php echo esc_attr( $type->name ); ?>" <?php selected( $post_type, $type->name ); ?>><?php echo esc_html( $type->labels->singular_name ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="brf_lf_posts_per_page"><?php esc_html_e( 'Posts Per Page', 'brf-live-filters' ); ?></label>
            <input id="brf_lf_posts_per_page" name="brf_lf_posts_per_page" type="number" class="small-text" value="<?php echo esc_attr( $posts_per_page ? $posts_per_page : 6 ); ?>" min="1" />
        </p>
        <p>
            <label for="brf_lf_orderby"><?php esc_html_e( 'Order By', 'brf-live-filters' ); ?></label>
            <select id="brf_lf_orderby" name="brf_lf_orderby" class="widefat">
                <option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'brf-live-filters' ); ?></option>
                <option value="title" <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'brf-live-filters' ); ?></option>
                <option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>><?php esc_html_e( 'Manual Order', 'brf-live-filters' ); ?></option>
            </select>
        </p>
        <p>
            <label for="brf_lf_order"><?php esc_html_e( 'Order', 'brf-live-filters' ); ?></label>
            <select id="brf_lf_order" name="brf_lf_order" class="widefat">
                <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending', 'brf-live-filters' ); ?></option>
                <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'brf-live-filters' ); ?></option>
            </select>
        </p>
        <?php
    }

    public static function render_layout_meta( $post ) {
        $layout          = get_post_meta( $post->ID, '_brf_lf_layout', true );
        $custom_template = get_post_meta( $post->ID, '_brf_lf_custom_template', true );
        ?>
        <p>
            <label for="brf_lf_layout"><?php esc_html_e( 'Layout', 'brf-live-filters' ); ?></label>
            <select id="brf_lf_layout" name="brf_lf_layout" class="widefat">
                <option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Grid', 'brf-live-filters' ); ?></option>
                <option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'List', 'brf-live-filters' ); ?></option>
                <option value="custom" <?php selected( $layout, 'custom' ); ?>><?php esc_html_e( 'Custom Template', 'brf-live-filters' ); ?></option>
            </select>
        </p>
        <p>
            <label for="brf_lf_custom_template"><?php esc_html_e( 'Custom Template File (optional)', 'brf-live-filters' ); ?></label>
            <input id="brf_lf_custom_template" name="brf_lf_custom_template" type="text" class="widefat" value="<?php echo esc_attr( $custom_template ); ?>" placeholder="templates/custom.php" />
            <small><?php esc_html_e( 'Provide a relative path within your theme.', 'brf-live-filters' ); ?></small>
        </p>
        <?php
    }

    public static function render_filters_meta( $post ) {
        $taxonomies  = get_post_meta( $post->ID, '_brf_lf_taxonomies', true );
        $meta_fields = get_post_meta( $post->ID, '_brf_lf_meta_fields', true );
        ?>
        <p><?php esc_html_e( 'Taxonomy filters (comma-separated slugs, e.g. category,post_tag,genre).', 'brf-live-filters' ); ?></p>
        <textarea name="brf_lf_taxonomies" class="widefat" rows="2"><?php echo esc_textarea( $taxonomies ); ?></textarea>
        <p><?php esc_html_e( 'Custom fields definition (one per line: key|type|label|choices). Type can be meta_text, meta_select, meta_boolean. Choices only for meta_select (comma separated).', 'brf-live-filters' ); ?></p>
        <textarea name="brf_lf_meta_fields" class="widefat" rows="4" placeholder="color|meta_select|Color|red,blue\nfeatured|meta_boolean|Featured"><?php echo esc_textarea( $meta_fields ); ?></textarea>
        <?php
    }

    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['brf_lf_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brf_lf_meta_nonce'] ) ), 'brf_lf_meta_nonce' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $post_type       = isset( $_POST['brf_lf_post_type'] ) ? sanitize_key( wp_unslash( $_POST['brf_lf_post_type'] ) ) : 'post';
        $posts_per_page  = isset( $_POST['brf_lf_posts_per_page'] ) ? absint( $_POST['brf_lf_posts_per_page'] ) : 6;
        $orderby         = isset( $_POST['brf_lf_orderby'] ) ? sanitize_key( wp_unslash( $_POST['brf_lf_orderby'] ) ) : 'date';
        $order           = isset( $_POST['brf_lf_order'] ) ? sanitize_text_field( wp_unslash( $_POST['brf_lf_order'] ) ) : 'DESC';
        $layout          = isset( $_POST['brf_lf_layout'] ) ? sanitize_key( wp_unslash( $_POST['brf_lf_layout'] ) ) : 'grid';
        $custom_template = isset( $_POST['brf_lf_custom_template'] ) ? sanitize_text_field( wp_unslash( $_POST['brf_lf_custom_template'] ) ) : '';

        update_post_meta( $post_id, '_brf_lf_post_type', $post_type );
        update_post_meta( $post_id, '_brf_lf_posts_per_page', $posts_per_page );
        update_post_meta( $post_id, '_brf_lf_orderby', $orderby );
        update_post_meta( $post_id, '_brf_lf_order', $order );
        update_post_meta( $post_id, '_brf_lf_layout', $layout );
        update_post_meta( $post_id, '_brf_lf_custom_template', $custom_template );

        $taxonomy_input = isset( $_POST['brf_lf_taxonomies'] ) ? sanitize_textarea_field( wp_unslash( $_POST['brf_lf_taxonomies'] ) ) : '';
        update_post_meta( $post_id, '_brf_lf_taxonomies', $taxonomy_input );

        $meta_input = isset( $_POST['brf_lf_meta_fields'] ) ? wp_unslash( $_POST['brf_lf_meta_fields'] ) : '';
        update_post_meta( $post_id, '_brf_lf_meta_fields', sanitize_textarea_field( $meta_input ) );
    }
}
