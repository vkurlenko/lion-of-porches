<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//(new Helper())->dump($related_products);
global $product;
/*(new Helper())->dump(get_terms('product_tag' ));
(new Helper())->dump($product->get_tag_ids()); //die;*/
//echo $product->get_id();

$featured_products = $product->is_type( 'variable' ) ? get_featured_custom($product) : [];


if ( $featured_products ) : ?>

    <section class="related products" style="clear: both">

        <?php
        $heading = 'Заверши свой образ';//apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

        if ( $heading ) :
            ?>
            <h2><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php foreach ( $featured_products as $related_product ) : ?>

            <?php
            $post_object = get_post( $related_product );//get_post( $related_product->get_id() );

            setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

            wc_get_template_part( 'content', 'product2' );
            ?>

        <?php endforeach; ?>

        <?php woocommerce_product_loop_end(); ?>

    </section>
<?php
endif;

wp_reset_postdata();


/*$featured_products = get_featured_custom($product->get_id());//get_related_custom($product->get_id());
$heading = 'Заверши свой образ';//apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

*/?><!--
    <section class="related products" style="clear: both">
    <h2><?php /*echo esc_html( $heading ); */?></h2>

        <?/*= $featured_products*/?>

    </section>

--><?php


/*$related_products = get_related_custom($product->get_id());


if ( $related_products ) : */?><!--

	<section class="related products" style="clear: both">

		<?php
/*		$heading = 'Заверши свой образ';//apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			*/?>
			<h2><?php /*echo esc_html( $heading ); */?></h2>
		<?php /*endif; */?>
		
		<?php /*woocommerce_product_loop_start(); */?>

			<?php /*foreach ( $related_products as $related_product ) : */?>

					<?php
/*					$post_object = get_post( $related_product );//get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product2' );
					*/?>

			<?php /*endforeach; */?>

		<?php /*woocommerce_product_loop_end(); */?>

	</section>
	--><?php
/*endif;

wp_reset_postdata();*/
