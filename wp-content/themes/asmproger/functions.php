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
?>