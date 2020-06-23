<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$current_user = wp_get_current_user();
$user_discount = (new Crm())->getUserDiscount($current_user->user_email);

if($user_discount) {
    $levels = (new Crm())->getUserLevel();

    $user_discount_level = isset($levels[$user_discount]) ? $levels[$user_discount] : 0;

    /*if($user_discount_level):
    */?><!--

    <div class="user-discount-level">
        <span class="user-name"><?/*=$current_user->display_name*/?></span>
        <span class="user-level"><?/*=$user_discount_level*/?></span>
        <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?/*=strtolower($user_discount_level)*/?>.jpg">
    </div>
    --><?php
/*    endif;*/
}

// у клиента есть карта (учетная запись в CRM)
$card_exists = !empty((new Crm())->getCrmUser());
//echo 'card_exists='.$card_exists;
?>

<?php
if(!$card_exists):?>

<div class="container">
    <div class="row">
        <div class="col-md-8">

<?php
endif;
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="row">
        <div>
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <!--<thead>-->
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <th class="product-thumbnail">&nbsp;</th>
                    <th class="product-name"><?php /*esc_html_e( 'Product', 'woocommerce' );*/ ?></th>
                    <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                    <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>

                    <?php if($card_exists):?>

                    <th id="td-invisible" rowspan="0"></th>

                    <th class="product-price" style="">
                        <div class="user-discount-level" style="">
                            <span class="user-level"><?=isset($user_discount_level) ? $user_discount_level : ''?></span>
                        </div>Ваша скидка
                    </th>

                    <th class="product-subtotal" style="">
                        <?php
                        if(isset($user_discount_level)):
                        ?>
                            <img id="user-level-label" style="" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>.jpg">
                        <?php
                        endif;
                        ?>
                        Сумма <br>с учетом Вашей скидки</th>

                    <?php endif;?>
                </tr>
                <!--</thead>
                <tbody>-->
                <?php do_action( 'woocommerce_before_cart_contents' ); ?>


                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

                    $wh = new WooHelper();
                    $cart_item_data = $wh->getCartItemData($cart_item);

                    //(new Helper())->dump($wh->getCartItemData($cart_item)); //die;

                    $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        ?>
                        <tr align="center" class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                            <td class="product-remove">
                                <?php
                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa fa-trash" aria-hidden="true"></i></a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_html__( 'Remove this item', 'woocommerce' ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </td>

                            <td class="product-thumbnail">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image([100,100]), $cart_item, $cart_item_key );

                                if ( ! $product_permalink ) {
                                    echo $thumbnail; // PHPCS: XSS ok.
                                } else {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                }
                                ?>
                            </td>

                            <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>" align="left">
                                <?php
                                if ( ! $product_permalink ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                } else {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                }
                                echo sprintf('<br><span class="sku_wrapper">арт: %s</span>', esc_attr( $_product->get_sku() ));

                                do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                // Meta data.
                                echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                                // Backorder notification.
                                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                }
                                ?>
                            </td>

                            <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                } else {
                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                            'min_value'    => '0',
                                            'product_name' => $_product->get_name(),
                                        ),
                                        $_product,
                                        false
                                    );
                                }

                                echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                                ?>
                            </td>

                            <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                <?php
                                if($cart_item_data['variation_regular_price'] != $cart_item_data['variation_price']){
                                    echo '<del>'.wc_price($cart_item_data['variation_regular_price']).'</del><br>';
                                }
                                ?>

                                <?php
                                echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                ?>
                            </td>





                            <?php if($card_exists):?>
                            <td class="product-price">
                                <?php
                                if($cart_item_data['user_percent']):?>
                                    <span>-<?=$cart_item_data['user_sale_percent_final'] ? ''.$cart_item_data['user_sale_percent_html'].'' : $cart_item_data['user_percent']?>%</span>
                                <?php
                                endif;
                                ?>
                            </td>

                            <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                <?php
                                if($cart_item_data['is_discount_price']){
                                    echo '<del>';
                                }
                                echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.

                                if($cart_item_data['is_discount_price']){
                                    echo '</del>';
                                    echo '<br>'.wc_price($cart_item_data['price_personal']);
                                }
                                ?>

                            </td>
                        <?php endif;?>
                        </tr>
                        <?php
                    }
                }
                ?>

                <!--</tbody>-->
            </table>

        </div>



    </div>

    <!-- обновление корзины -->
    <?php do_action( 'woocommerce_cart_contents' ); ?>
    <?php if ( wc_coupons_enabled() ) { ?>
        <div class="coupon">
            <label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
            <?php do_action( 'woocommerce_cart_coupon' ); ?>
        </div>
    <?php } ?>

    <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

    <?php do_action( 'woocommerce_cart_actions' ); ?>

    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
    <!-- /обновление корзины -->

	<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>


<div class="cart-collaterals ">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>


<?php do_action( 'woocommerce_after_cart' ); ?>

<?php
if(!$card_exists):
?>
        </div>

        <div class="col-md-3 col-md-offset-1 product-card-personal">
            <div>
                <div class="product-card-personal-inner">
                    <?=get_template_part( 'parts/guest-block');?>

                    <div class="product">
                        <div class="page-sep">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAVCAMAAABxCz6aAAAAeFBMVEVMaXHeChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChpoOs6ZAAAAJ3RSTlMA8OCgMMBQEIBgcJALA0ywDHyXCIhAtQEuPeICavcYEnmHHg7ECqhJSmy5AAAAoklEQVR42m3Q2RKDIAwFUEpZq1ar3fc9//+HvYbMUGvvQyYcogwo5YkoVcQrTsbFPGNZE8X7Q2fkYNRx7fFijOEfXL8wovLojG7OYWFtA9RWMAUtMKj/OEFOv4i+LYqi7TEgtaCXK0iA2OyAXWBYYrlSnNcUGdw9fzvGGOORaF0maax90tvJns9jOHGM5izdrkpYyVtpvefBQQjZHsboN6n9AL9IGjFswE2+AAAAAElFTkSuQmCC" alt="Lion of Porches">
                        </div>
                    </div>
                    <div class="offerta">
                        <a href="#">Условия и лимиты программы привилегий</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
endif;
?>


