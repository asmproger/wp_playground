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
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query = new WP_Query([
                    'post_type' => 'book',
                    'posts_per_page' => 10,
                    'paged' => $paged,
            ]);

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
                                do_action('show_meta_custom_hook');
                            ?>
                        </td>
                    </tr>
                </table>
            <?php endwhile; ?>
            <?php

            $total_pages = $query->max_num_pages;

            if ($total_pages > 1){
                $current_page = max(1, get_query_var('paged'));
                echo paginate_links(array(
                    'base' => get_pagenum_link(1) . '%_%',
                    'format' => 'page/%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text'    => __('« prev'),
                    'next_text'    => __('next »'),
                ));
            }
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
do_action('storefront_sidebar');
get_footer();
?>