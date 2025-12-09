<?php
/**
 * List layout template.
 *
 * @var WP_Query $query
 * @var array    $settings
 */
?>
<ul class="brf-lf-list">
    <?php if ( $query->have_posts() ) : ?>
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <li <?php post_class( 'brf-lf-item' ); ?>>
                <h3 class="brf-lf-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="brf-lf-meta"><?php echo esc_html( get_the_date() ); ?></p>
                <p class="brf-lf-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
            </li>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <li class="brf-lf-empty"><?php esc_html_e( 'No results found.', 'brf-live-filters' ); ?></li>
    <?php endif; ?>
</ul>
