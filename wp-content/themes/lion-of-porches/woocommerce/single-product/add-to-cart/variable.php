<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );


/*$i = 0;
foreach ($available_variations as $variation) {
    echo key($available_variations);
    $available_variations[$i++]['display_price'] = 100;
}*/

//(new Helper())->dump($available_variations); //die;

if ( is_user_logged_in() ) {

    $current_user = wp_get_current_user();

    // персональная скидка клиента (%)
    $discount = (int)(new Crm())->getUserDiscount($current_user->user_email);

    if($discount) {

        // персональная скидка клиента (коэффициент)
        $p = $discount / 100;

        $i = 0;
        foreach($available_variations as $variation) {

            // если на вариацию нет скидки
            if($variation['display_price'] === $variation['display_regular_price']) {

                // цена на товар с учетом скидки
                $discount_price = $variation['display_regular_price'] - $variation['display_regular_price'] * $p;

                $available_variations[$i]['display_price'] = $discount_price;

                $text = sprintf('<span class="price" style="display: block;"><del>%s</del></span>', wc_price( $variation['display_regular_price'] ));
                $text .= sprintf('<span class="price" style="display: block;"><ins>%s</ins></span>', wc_price(  $discount_price  ));
                $text .= '<div class="discount-personal"><div class="discount-value">Ваша персональная скидка: - <span>'.$discount.'</span>%</div>';
                //$text .= '<p class="price">Стоимость с учётом Вашей скидки ' . wc_price( $discount_price ). '</p>';
                $text .= '<p class="price s">Ваша экономия: ' . wc_price( +$variation['display_regular_price'] - $discount_price ). '</p></div>';

                $available_variations[$i]['price_html'] = $text;
            }
            $i++;
        }
    }



    /*if($discount_price) {
        $hidden = $is_variation_price ? '' : '';
        $price .= '<div class="discount-personal" '.$hidden.'><div class="discount-value">Ваша персональная скидка - <span>'.$discount.'</span>%</div>';
        $price .= '<p class="price">Стоимость с учётом Вашей скидки ' . wc_price( $discount_price ). '</p>';
        $price .= '<p class="price s">Ваша экономия ' . wc_price( $s ). '</p></div>';
    }*/
} /*else {
    $price .= '<span class="symbol">' . sprintf(get_woocommerce_currency_symbol() ) . '</span>';
}*/



//(new Helper())->dump($available_variations);
$variations_json = wp_json_encode( $available_variations );



$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<!--<td class="label"><label for="<?php /*echo esc_attr( sanitize_title( $attribute_name ) ); */?>"><?php /*echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. */?></label></td>-->
						<td class="value">
							<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								//echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>

                <tr>
                    <!--<td></td>-->
                    <td>
                        <div class="sizeguide-link">
                            <a href="/shop/sizeguide/" target="_blank"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAAAW0lEQVRYhWNgGAWjYBSMdMDIoOL9fyAdwDSQljMwMDCwwFl3tjLC2bBQgYlRm48kNuAhMOoAFsJKGFDjjsryAx4CA+4ARqxZhB5gNBuOOgAKRmvDUTAKRsEoAAAo2iEi024MXwAAAABJRU5ErkJggg==">Размерная сетка</a>
                        </div>
                        <?php
                        //echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#"><i class="fa fa-undo" aria-hidden="true"></i>' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
                        ?>
                    </td>
                </tr>

			</tbody>
		</table>



		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
