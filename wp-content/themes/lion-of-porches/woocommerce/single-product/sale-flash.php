<?php
/**
 * Single Product Sale Flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<div class="flash-tags">
<?php if ( $product->is_on_sale() ) : ?>

	<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product ); ?>

	<?php
endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
$posttags = get_the_terms( $product->get_id(), 'product_tag' );

if($posttags) {
    foreach($posttags as $tag) {

        if(!in_array($tag->slug, (new WooHelper())->getVisibleTags())) {
            continue;
        }

        ?>
        <?php echo apply_filters( 'woocommerce_sale_flash', '<a href="/product-tag/'.$tag->slug.'/"><span class="prod-tag '.$tag->slug.'">'.$tag->name.'</span></a>', $post, $product ); ?>
        <?php
    }
}
?>
</div>
