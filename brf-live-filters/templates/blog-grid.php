<?php
/**
 * Blog-style card grid template inspired by WP Gridbuilder demo.
 *
 * @var WP_Query $query
 * @var array    $settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Avoid printing the style block multiple times on the same page.
static $brf_lf_blog_style_printed = false;

if ( ! $brf_lf_blog_style_printed ) :
    $brf_lf_blog_style_printed = true;
    ?>
    <style>
        .brf-lf-blog-grid {
            display: grid;
            gap: 32px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .brf-lf-blog-grid .brf-lf-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .brf-lf-blog-grid .brf-lf-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
        }

        .brf-lf-card-thumb {
            position: relative;
            aspect-ratio: 16 / 10;
            background: #f5f5f5;
        }

        .brf-lf-card-thumb a,
        .brf-lf-card-thumb img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brf-lf-card-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px 22px 22px;
        }

        .brf-lf-card-cats {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .brf-lf-card-cat {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .brf-lf-card-title {
            margin: 0;
            font-size: 22px;
            line-height: 1.35;
        }

        .brf-lf-card-title a {
            color: #0f172a;
            text-decoration: none;
        }

        .brf-lf-card-title a:hover {
            color: #1d4ed8;
        }

        .brf-lf-card-meta {
            display: flex;
            gap: 14px;
            align-items: center;
            color: #6b7280;
            font-size: 13px;
        }

        .brf-lf-card-excerpt {
            margin: 0;
            color: #374151;
            font-size: 15px;
            line-height: 1.6;
        }

        .brf-lf-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 8px;
        }

        .brf-lf-card-readmore {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #1d4ed8;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            letter-spacing: 0.01em;
        }

        .brf-lf-card-readmore svg {
            width: 14px;
            height: 14px;
        }

        .brf-lf-card-author {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 13px;
        }

        .brf-lf-card-author img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
<?php endif; ?>

<div class="brf-lf-blog-grid">
    <?php if ( $query->have_posts() ) : ?>
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <article <?php post_class( 'brf-lf-card' ); ?>>
                <div class="brf-lf-card-thumb">
                    <a href="<?php the_permalink(); ?>">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large' );
                        } else {
                            echo '<span style="display:block;width:100%;height:100%;background:linear-gradient(135deg,#f8fafc,#e5e7eb);"></span>';
                        }
                        ?>
                    </a>
                </div>

                <div class="brf-lf-card-body">
                    <?php $categories = get_the_category(); ?>
                    <?php if ( ! empty( $categories ) ) : ?>
                        <ul class="brf-lf-card-cats">
                            <?php foreach ( $categories as $category ) : ?>
                                <li class="brf-lf-card-cat"><?php echo esc_html( $category->name ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <h3 class="brf-lf-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                    <div class="brf-lf-card-meta">
                        <span class="brf-lf-card-date"><?php echo esc_html( get_the_date() ); ?></span>
                        <span class="brf-lf-card-sep">•</span>
                        <span class="brf-lf-card-reading"><?php echo esc_html( sprintf( _n( '%s min read', '%s mins read', 3, 'brf-live-filters' ), 3 ) ); ?></span>
                    </div>

                    <p class="brf-lf-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>

                    <div class="brf-lf-card-footer">
                        <div class="brf-lf-card-author">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 28 ); ?>
                            <span><?php echo esc_html( get_the_author() ); ?></span>
                        </div>
                        <a class="brf-lf-card-readmore" href="<?php the_permalink(); ?>">
                            <?php esc_html_e( 'Read more', 'brf-live-filters' ); ?>
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.5 4.5 13 10l-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="brf-lf-empty"><?php esc_html_e( 'No results found.', 'brf-live-filters' ); ?></p>
    <?php endif; ?>
</div>
