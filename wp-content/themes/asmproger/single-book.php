<?php
/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

get_header(); ?>
    <script>
        (function ($) {
            function clearForm() {
                $('#asmp_user_email').val('');
                $('#asmp_price').val('');
                $('#asmp_currency').val('');
            }

            $(document).on('click', "#asmp-submit-propose", function (e) {
                var data = {
                    action: 'amsp_propose',
                    book_id: $('#book_id').val(),
                    email: $('#asmp_user_email').val(),
                    price: $('#asmp_price').val(),
                    currency: $('#asmp_currency').val()
                };

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php', 'relative'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            alert('success');
                            clearForm();
                        } else {
                            if (response.code == 1) {
                                alert('data error. incorrect or incomplete data.\n check form.');
                            } else {
                                alert('database err. try again later.');
                            }
                        }
                    }
                });

            });
        })(jQuery);
    </script>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php while (have_posts()) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                    do_action('storefront_single_post_top'); ?>
                    <div class="entry-content">
                        <?php storefront_post_header(); ?>
                        <table class="single-book-table">
                            <tr>
                                <td class="left">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail($size);
                                    }
                                    do_action('show_meta_custom_hook');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    the_content(
                                        sprintf(
                                            __('Continue reading %s', 'storefront'),
                                            '<span class="screen-reader-text">' . get_the_title() . '</span>'
                                        )
                                    );

                                    do_action('storefront_post_content_after');

                                    wp_link_pages(array(
                                        'before' => '<div class="page-links">' . __('Pages:', 'storefront'),
                                        'after' => '</div>',
                                    ));
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <div class="asmp-book-proposition-form">
                            <?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                            if (is_plugin_active('asmproger-plugin/asmproger-plugin.php')): ?>
                                <?php $propositions = $asmpInstance->getPropositions(); ?>
                                <?php if ($propositions && count($propositions)): ?>
                                    <p>Propositions: </p>
                                    <ul class="asmp-book-propositions">
                                        <?php foreach ($propositions as $item): ?>
                                            <li>
                                                <?php echo $asmpInstance->getProposition($item); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            <?php endif; ?>
                            <h4 class="asmp-book-proposition-form-title">Your proposition?</h4>
                            <form method="post">
                                <div>
                                    <label for="asmp_user_email">Email</label>
                                    <input type="email" name="asmp-book-proposition-form[email]" id="asmp_user_email">
                                </div>
                                <div>
                                    <label for="asmp_price">Price</label>
                                    <input type="number" min="1" name="asmp-book-proposition-form[price]"
                                           id="asmp_price">
                                </div>
                                <div>
                                    <label for="asmp_currency">Currency</label>
                                    <select name="asmp-book-proposition-form[currency]" id="asmp_currency">
                                        <?php
                                        $cs = $asmpInstance->getAllowedCurrencies();
                                        foreach ($cs as $k => $v): ?>
                                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <input type="hidden" id="book_id" name="book_id" value="<?php echo get_the_ID(); ?>">
                                    <button type="button" id="asmp-submit-propose">Go!</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    /**
                     * Functions hooked in to storefront_single_post_bottom action
                     *
                     * @hooked storefront_post_nav         - 10
                     * @hooked storefront_display_comments - 20
                     */
                    do_action('storefront_single_post_bottom');
                    ?>
                </div><!-- #post-## -->
            <?php endwhile; ?>
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
    dynamic_sidebar('asmp_custom_sidebar');
    do_action('storefront_sidebar');
    get_footer();
?>