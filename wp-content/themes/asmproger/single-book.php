<?php
/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

get_header(); ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php while ( have_posts() ) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                        do_action( 'storefront_single_post_top' ); ?>
                        <div class="entry-content">
                            <?php storefront_post_header(); ?>
                            <table>
                                <tr>
                                    <td style="width: 300px;">
                                        <?php
                                            if ( has_post_thumbnail() ) {
                                                the_post_thumbnail( $size );
                                            }
                                            WP_Asmproger_Plugin::echoMeta();
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            the_content(
                                                sprintf(
                                                    __( 'Continue reading %s', 'storefront' ),
                                                    '<span class="screen-reader-text">' . get_the_title() . '</span>'
                                                )
                                            );

                                            do_action( 'storefront_post_content_after' );

                                            wp_link_pages( array(
                                                'before' => '<div class="page-links">' . __( 'Pages:', 'storefront' ),
                                                'after'  => '</div>',
                                            ) );
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    <?php
                        /**
                         * Functions hooked in to storefront_single_post_bottom action
                         *
                         * @hooked storefront_post_nav         - 10
                         * @hooked storefront_display_comments - 20
                         */
                        do_action( 'storefront_single_post_bottom' );
                    ?>
                </div><!-- #post-## -->
            <?php endwhile; ?>
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
    do_action( 'storefront_sidebar' );
    get_footer();
?>