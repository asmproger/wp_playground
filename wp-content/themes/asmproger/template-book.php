<?php
/**
 * Template name: book
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
                $query = new WP_Query(['post_type' => 'book']);
                while($query->have_posts()): $query->the_post();
            ?>
                <table class="book-item" data-id="<?php the_ID(); ?>">
                    <tr>
                        <td class="img">
                            <?php
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( $size );
                                }
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                                $link = get_permalink();
                                echo the_title('<h4><a href="'.$link.'">', '</a></h4>');
                                echo get_post_meta(get_the_ID(), 'Writer', 1);
                            ?>
                            author
                        </td>
                    </tr>
                </table>

            <?php endwhile; ?>
            <?php while (have_posts()) : the_post(); ?>

            <?php endwhile; ?>
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
do_action('storefront_sidebar');
get_footer();
?>