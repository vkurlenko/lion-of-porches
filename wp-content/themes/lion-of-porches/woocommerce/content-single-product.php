<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

    <div class="visible-xs">
    <?php wc_get_template( 'single-product/title.php' );?>

        <?php wc_get_template( 'single-product/price.php' );?>
    </div>

	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	/* картинка товара */
	do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		/* стоимость, выбор атрибутов, кнопка Купить */
		do_action( 'woocommerce_single_product_summary' );

        //$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) );
		?>
        <?php /*if ( $heading ) : */?><!--
            <h2><?php /*echo esc_html( $heading ); */?></h2>
        --><?php /*endif; */?>

        <p>
        <?php the_content(); ?>
        </p>
	</div>

    <!-- delimiter -->
    <div class="page-sep col-xs-12">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAVCAMAAABxCz6aAAAAeFBMVEVMaXHeChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChpoOs6ZAAAAJ3RSTlMA8OCgMMBQEIBgcJALA0ywDHyXCIhAtQEuPeICavcYEnmHHg7ECqhJSmy5AAAAoklEQVR42m3Q2RKDIAwFUEpZq1ar3fc9//+HvYbMUGvvQyYcogwo5YkoVcQrTsbFPGNZE8X7Q2fkYNRx7fFijOEfXL8wovLojG7OYWFtA9RWMAUtMKj/OEFOv4i+LYqi7TEgtaCXK0iA2OyAXWBYYrlSnNcUGdw9fzvGGOORaF0maax90tvJns9jOHGM5izdrkpYyVtpvefBQQjZHsboN6n9AL9IGjFswE2+AAAAAElFTkSuQmCC" alt="Lion of Porches">
    </div>
    <!-- /delimiter -->

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */

	/* мета-данные товара*/
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
