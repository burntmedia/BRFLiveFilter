<?php
/**
 * Grid layout template.
 *
 * @var WP_Query $query
 * @var array    $settings
 */
?>
<div class="brf-lf-grid">
    <?php if ( $query->have_posts() ) : ?>
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <article <?php post_class( 'brf-lf-item' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                    <a class="brf-lf-thumb" href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'medium' ); ?>
                    </a>
                <?php endif; ?>
                <h3 class="brf-lf-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="brf-lf-meta"><?php echo esc_html( get_the_date() ); ?></p>
                <p class="brf-lf-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
            </article>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="brf-lf-empty"><?php esc_html_e( 'No results found.', 'brf-live-filters' ); ?></p>
    <?php endif; ?>
</div>
