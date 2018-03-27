<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 3/22/18
 * Time: 4:05 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
<p class="price"><?php echo $product->get_price_html(); ?></p>
