<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 class="product_title entry-title">', '</h1>' );



/*$current_user = wp_get_current_user();
$user_discount = (new Crm())->getUserDiscount($current_user->user_email);

if(!$user_discount) {
    return false;
}

$user_discount_level = (new Crm())->getUserLevel()[$user_discount];
/*echo 'level='.$user_discount_level;
(new Helper())->dump($current_user);*/

?><!--

<div class="user-discount-level">
    <span class="user-name"><?=$current_user->display_name?></span>
    <span class="user-level"><?=$user_discount_level?></span>
    <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>.jpg">
</div>-->

<?php


global $product;
if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

    <div class="sku_wrapper">арт: <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></div>

<?php endif; ?>
