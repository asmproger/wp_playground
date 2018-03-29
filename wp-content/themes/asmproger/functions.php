<?php
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

add_action('woocommerce_after_single_product_summary', 'wtf', 7);
function wtf()
{
    echo '<h5>test hook here.</h5>';
}

//add_action('wp_head', 'show_template');
function show_template() {
    global $template;
    echo basename($template);
}

function registerSidebar() {
    register_sidebar([
        'name' => 'Custom sidebar',
        'id' => 'asmp_custom_sidebar',
        'description' => 'Custom sidebar description',
        'class' => 'asmp_custom_sidebar',
        'before_widget' => '<ul>',
        'after_widget' => '</ul>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ]);
}
add_action('init', 'registerSidebar');

require_once 'Asmp_Propositions.php';
add_action('widgets_init', function() {
    register_widget('Asmp_Propositions');
});
?>