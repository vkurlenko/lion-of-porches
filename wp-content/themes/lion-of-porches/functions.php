<?php
/**
 * Функции шаблона (function.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */

include 'Helper.php';
//include_once 'Crm.php';
//include_once 'WooHelper.php';
$helper = new Helper();


add_theme_support('title-tag'); // теперь тайтл управляется самим вп

/** woocommerce */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
//add_filter( 'woocommerce_enqueue_styles', '__return_false' );
/** /woocommerce */

register_nav_menus(array( // Регистрируем 2 меню
	'top' => 'Верхнее', // Верхнее
	'bottom' => 'Внизу' // Внизу
));

add_theme_support('post-thumbnails'); // включаем поддержку миниатюр
set_post_thumbnail_size(250, 150); // задаем размер миниатюрам 250x150
add_image_size('big-thumb', 400, 400, true); // добавляем еще один размер картинкам 400x400 с обрезкой

register_sidebar(array( // регистрируем левую колонку, этот кусок можно повторять для добавления новых областей для виджитов
	'name' => 'Сайдбар', // Название в админке
	'id' => "sidebar", // идентификатор для вызова в шаблонах
	'description' => 'Обычная колонка в сайдбаре', // Описалово в админке
	'before_widget' => '<div id="%1$s" class="widget %2$s">', // разметка до вывода каждого виджета
	'after_widget' => "</div>\n", // разметка после вывода каждого виджета
	'before_title' => '<span class="widgettitle">', //  разметка до вывода заголовка виджета
	'after_title' => "</span>\n", //  разметка после вывода заголовка виджета
));

if (!class_exists('clean_comments_constructor')) { // если класс уже есть в дочерней теме - нам не надо его определять
	class clean_comments_constructor extends Walker_Comment { // класс, который собирает всю структуру комментов
		public function start_lvl( &$output, $depth = 0, $args = array()) { // что выводим перед дочерними комментариями
			$output .= '<ul class="children">' . "\n";
		}
		public function end_lvl( &$output, $depth = 0, $args = array()) { // что выводим после дочерних комментариев
			$output .= "</ul><!-- .children -->\n";
		}
	    protected function comment( $comment, $depth, $args ) { // разметка каждого комментария, без закрывающего </li>!
	    	$classes = implode(' ', get_comment_class()).($comment->comment_author_email == get_the_author_meta('email') ? ' author-comment' : ''); // берем стандартные классы комментария и если коммент пренадлежит автору поста добавляем класс author-comment
	        echo '<li id="comment-'.get_comment_ID().'" class="'.$classes.' media">'."\n"; // родительский тэг комментария с классами выше и уникальным якорным id
	    	echo '<div class="media-left">'.get_avatar($comment, 64, '', get_comment_author(), array('class' => 'media-object'))."</div>\n"; // покажем аватар с размером 64х64
	    	echo '<div class="media-body">';
	    	echo '<span class="meta media-heading">Автор: '.get_comment_author()."\n"; // имя автора коммента
	    	//echo ' '.get_comment_author_email(); // email автора коммента, плохой тон выводить почту
	    	echo ' '.get_comment_author_url(); // url автора коммента
	    	echo ' Добавлено '.get_comment_date('F j, Y в H:i')."\n"; // дата и время комментирования
	    	if ( '0' == $comment->comment_approved ) echo '<br><em class="comment-awaiting-moderation">Ваш комментарий будет опубликован после проверки модератором.</em>'."\n"; // если комментарий должен пройти проверку
	    	echo "</span>";
	        comment_text()."\n"; // текст коммента
	        $reply_link_args = array( // опции ссылки "ответить"
	        	'depth' => $depth, // текущая вложенность
	        	'reply_text' => 'Ответить', // текст
				'login_text' => 'Вы должны быть залогинены' // текст если юзер должен залогинеться
	        );
	        echo get_comment_reply_link(array_merge($args, $reply_link_args)); // выводим ссылку ответить
	        echo '</div>'."\n"; // закрываем див
	    }
	    public function end_el( &$output, $comment, $depth = 0, $args = array() ) { // конец каждого коммента
			$output .= "</li><!-- #comment-## -->\n";
		}
	}
}

if (!function_exists('pagination')) { // если ф-я уже есть в дочерней теме - нам не надо её определять
	function pagination() { // функция вывода пагинации
		global $wp_query; // текущая выборка должна быть глобальной
		$big = 999999999; // число для замены
		$links = paginate_links(array( // вывод пагинации с опциями ниже
			'base' => str_replace($big,'%#%',esc_url(get_pagenum_link($big))), // что заменяем в формате ниже
			'format' => '?paged=%#%', // формат, %#% будет заменено
			'current' => max(1, get_query_var('paged')), // текущая страница, 1, если $_GET['page'] не определено
			'type' => 'array', // нам надо получить массив
			'prev_text'    => 'Назад', // текст назад
	    	'next_text'    => 'Вперед', // текст вперед
			'total' => $wp_query->max_num_pages, // общие кол-во страниц в пагинации
			'show_all'     => false, // не показывать ссылки на все страницы, иначе end_size и mid_size будут проигнорированны
			'end_size'     => 15, //  сколько страниц показать в начале и конце списка (12 ... 4 ... 89)
			'mid_size'     => 15, // сколько страниц показать вокруг текущей страницы (... 123 5 678 ...).
			'add_args'     => false, // массив GET параметров для добавления в ссылку страницы
			'add_fragment' => '',	// строка для добавления в конец ссылки на страницу
			'before_page_number' => '', // строка перед цифрой
			'after_page_number' => '' // строка после цифры
		));
	 	if( is_array( $links ) ) { // если пагинация есть
		    echo '<ul class="pagination">';
		    foreach ( $links as $link ) {
		    	if ( strpos( $link, 'current' ) !== false ) echo "<li class='active'>$link</li>"; // если это активная страница
		        else echo "<li>$link</li>"; 
		    }
		   	echo '</ul>';
		 }
	}
}

add_action('wp_footer', 'add_scripts'); // приклеем ф-ю на добавление скриптов в футер
if (!function_exists('add_scripts')) { // если ф-я уже есть в дочерней теме - нам не надо её определять
	function add_scripts() { // добавление скриптов
        $version = '2.7';
	    if(is_admin()) return false; // если мы в админке - ничего не делаем
	    wp_deregister_script('jquery'); // выключаем стандартный jquery
	    wp_enqueue_script('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js','','',true); // добавляем свой
	    wp_enqueue_script('bootstrap', get_template_directory_uri().'/js/bootstrap.min.js','','',true); // бутстрап
        wp_enqueue_script('slick-slider', get_template_directory_uri() . '/js/slick/slick.min.js', '', '', true );
	    wp_enqueue_script('main', get_template_directory_uri().'/js/main.js','jquery', $version,true); // и скрипты шаблона
	}
}

add_action('wp_print_styles', 'add_styles'); // приклеем ф-ю на добавление стилей в хедер
if (!function_exists('add_styles')) { // если ф-я уже есть в дочерней теме - нам не надо её определять
	function add_styles() { // добавление стилей
        $version = '2.7';
	    if(is_admin()) return false; // если мы в админке - ничего не делаем
	    wp_enqueue_style( 'bs', get_template_directory_uri().'/css/bootstrap.min.css' ); // бутстрап
        //wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css' );
		wp_enqueue_style( 'main', get_template_directory_uri().'/style.css', '', $version ); // основные стили шаблона
        wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/js/slick/slick.css' );
        wp_enqueue_style( 'slick-style-theme', get_template_directory_uri() . '/js/slick/slick-theme.css' );
	}
}

if (!class_exists('bootstrap_menu')) {
	class bootstrap_menu extends Walker_Nav_Menu { // внутри вывод 
		private $open_submenu_on_hover; // параметр который будет определять раскрывать субменю при наведении или оставить по клику как в стандартном бутстрапе

		function __construct($open_submenu_on_hover = true) { // в конструкторе
	        $this->open_submenu_on_hover = $open_submenu_on_hover; // запишем параметр раскрывания субменю
	    }

		function start_lvl(&$output, $depth = 0, $args = array()) { // старт вывода подменюшек
			$output .= "\n<ul class=\"dropdown-menu\">\n"; // ул с классом
		}
		function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) { // старт вывода элементов
			$item_html = ''; // то что будет добавлять
			parent::start_el($item_html, $item, $depth, $args); // вызываем стандартный метод родителя
			if ( $item->is_dropdown && $depth === 0 ) { // если элемент содержит подменю и это элемент первого уровня
			   if (!$this->open_submenu_on_hover) $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"', $item_html); // если подменю не будет раскрывать при наведении надо добавить стандартные атрибуты бутстрапа для раскрытия по клику
			   $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html); // ну это стрелочка вниз
			}
			$output .= $item_html; // приклеиваем теперь
		}
		function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) { // вывод элемента
			if ( $element->current ) $element->classes[] = 'active'; // если элемент активный надо добавить бутстрап класс для подсветки
			$element->is_dropdown = !empty( $children_elements[$element->ID] ); // если у элемента подменю
			if ( $element->is_dropdown ) { // если да
			    if ( $depth === 0 ) { // если li содержит субменю 1 уровня
			        $element->classes[] = 'dropdown'; // то добавим этот класс
			        if ($this->open_submenu_on_hover) $element->classes[] = 'show-on-hover'; // если нужно показывать субменю по хуверу
			    } elseif ( $depth === 1 ) { // если li содержит субменю 2 уровня
			        $element->classes[] = 'dropdown-submenu'; // то добавим этот класс, стандартный бутстрап не поддерживает подменю больше 2 уровня по этому эту ситуацию надо будет разрешать отдельно
			    }
			}
			parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output); // вызываем стандартный метод родителя
		}
	}
}

if (!function_exists('content_class_by_sidebar')) { // если ф-я уже есть в дочерней теме - нам не надо её определять
	function content_class_by_sidebar() { // функция для вывода класса в зависимости от существования виджетов в сайдбаре
		if (is_active_sidebar( 'sidebar' )) { // если есть
			echo 'col-sm-9'; // пишем класс на 80% ширины
		} else { // если нет
			echo 'col-sm-12'; // контент на всю ширину
		}
	}
}

// правильный способ подключить стили и скрипты темы
//add_action( 'wp_enqueue_scripts', 'theme_add_scripts' );

function theme_add_scripts() {
    // подключаем файл стилей темы
    /*wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/js/slick/slick.css' );
    wp_enqueue_style( 'slick-style-theme', get_template_directory_uri() . '/js/slick/slick-theme.css' );*/

    // подключаем js файл темы
    //wp_enqueue_script( 'slick-slider', get_template_directory_uri() . '/js/slick/slick.min.js', array('jquery'), '', true );
}
/******************/
/* My Woocommerce */
/******************/

// переместил "Показ всех 3 элементов" вниз страницы
remove_action ( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action ( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );

add_action( 'user_register', 'my_user_registration' );
function my_user_registration( $user_id ) {
    // $_POST['user_sex'] проверена заранее...
    //update_user_meta( $user_id, 'user_sex', $_POST['user_sex']);
    setcookie( 'just_register', $user_id, time()+31556926 );
}

function woocommerce_template_loop_category_title($category ) {
    ?>
	<h2 class="woocommerce-loop-category__title">
        <?php
        echo esc_html( $category->name );

        /*if ( $category->count > 0 ) {
            echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category ); // WPCS: XSS ok.
        }*/
        ?>
	</h2>
    <?php
}

add_filter( 'add_to_cart_text', 'woo_custom_single_add_to_cart_text' );                // < 2.1
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_single_add_to_cart_text' );  // 2.1 +

function woo_custom_single_add_to_cart_text() {

    return __( "Купить", 'woocommerce' );

}

/**
 * Вывод вариантов цветов товара на карточке товара в каталоге
 *
 */
function woocommerce_template_loop_product_title() {

    global $product;

    $woo_helper = new WooHelper();
    //(new Helper())->dump($product); die;
    $sku = explode('.', $product->get_sku());
    echo  '<span class="art">'.$sku[0].'</span>';

    echo '<div class="colors-bar">';
    $args = array(
        'post_type'     => 'product_variation',
        'post_status'   => array( 'private', 'publish' ),
        'numberposts'   => -1,
        'orderby'       => 'menu_order',
        'order'         => 'ASC',
        'post_parent'   => get_the_ID() // get parent post-ID
    );
    $variations = get_posts( $args );

    $colors = [];
    $titles = $woo_helper->getColorTitles();

    foreach ( $variations as $variation ) {

        // get variation ID
        $variation_ID = $variation->ID;

        // get variations meta
        $product_variation = new WC_Product_Variation( $variation_ID );

        // get variation featured image
        $variation_image = $product_variation->get_image();

        // get variation price
        $variation_price = $product_variation->get_price_html();

        //get variation name
        $variation_name = $product_variation->get_variation_attributes();

        if(!in_array($variation_name [ 'attribute_pa_color'], $colors)) {
            $colors[] = $variation_name [ 'attribute_pa_color'];
            $color_title = isset($titles[$variation_name [ 'attribute_pa_color']]) ? $titles[$variation_name [ 'attribute_pa_color']] : '';
            //echo '<span class="color '.$variation_name [ 'attribute_pa_color'].'" title="'.$color_title.'"></span>';
        }
    }
    echo '</div>';


    if((new Crm())->getCurrentUserDiscount()) {
        $discount = sprintf('-%s%%', (new Crm())->getCurrentUserDiscount());
    } else {
        $discount = '';
    }

    //echo '<span class="personal-discount">'.$discount.'</span>';

    echo '<h1 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
/************************************************************/
/************************************************************/

remove_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );
remove_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );


/*add_filter('woocommerce_get_image_size_thumbnail','add_thumbnail_size',1,10);
function add_thumbnail_size($size){

    $size['width'] = 250;
    $size['height'] = 300;
    $size['crop']   = 0;
    return $size;
}*/


/**
 * Меню в личном кабинете
 *
 */
add_filter ( 'woocommerce_account_menu_items', 'my_account_links' );

function my_account_links($menu_links ){

    //unset( $menu_links['edit-address'] ); // Addresses
    unset( $menu_links['dashboard'] ); // Dashboard
    //unset( $menu_links['payment-methods'] ); // Payment Methods
    //unset( $menu_links['orders'] ); // Orders
    unset( $menu_links['downloads'] ); // Downloads
    //unset( $menu_links['edit-account'] ); // Account details
    //unset( $menu_links['customer-logout'] ); // Logout

    return $menu_links;
}
/******************************************/
/******************************************/


/**
 * Our hooked in function - $fields is passed via the filter!
 *
 * @param $fields
 * @return mixed
 */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields($fields ) {

    unset($fields['billing']['billing_company']); // удаляем Название компании
    //unset($fields['billing']['billing_postcode']); // удаляем Индекс
    //unset($fields['billing']['billing_city']); // удаляем Населённый пункт
    unset($fields['billing']['billing_country']); // удаляем поле Страна
    unset($fields['billing']['billing_address_2']); // удаляем второе поле Адрес
    unset($fields['shipping']['shipping_country']); ////удаляем! тут хранится значение страны доставки

    /*$fields['billing']['billing_address_1']['label'] = 'Номер отделения Новой Почты'; // меняем Адрес
    $fields['billing']['billing_address_1']['placeholder'] = ' '; // в поле Адрес оставляем пустым*/
    return $fields;
}

add_filter('wc_order_is_editable', 'my_wc_order_is_editable', 10, 2);

/**
 * Разрешить редактировать заказ из админки
 * @param $res
 * @param $order
 * @return bool
 */
function my_wc_order_is_editable($res, $order) {

    if(in_array($order->get_status(), array('processing', 'cancelled'))) {
        return true;
    }

    return $res;
}


/**
 * Пересчет стоимости корзины с учетом персональной скидки
 *
 * @param WC_Cart $cart
 */
add_action("woocommerce_cart_calculate_fees" , "woo_discount_total");

function woo_discount_total(WC_Cart $cart) {

    //(new Helper())->dump($cart); die;

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();

        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        if($discount) {

            $p = $discount / 100;

            //$discount_price = $cart->subtotal * $p;//0.05; // 0.05 - это 5%

            $discount_price = (new WooHelper())->getCartSubTotal($cart);

            //$cart->add_fee("Персональная скидка в ".$discount."% ", -$discount_price);
            $cart->add_fee("Ваша&nbsp;экономия", -$discount_price);
        }
    }
}

add_action( 'woocommerce_before_calculate_totals', 'set_custom_cart_item_price', 10, 1 );

function set_custom_cart_item_price( $wc_cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        if($discount == 50) {
            foreach ( $wc_cart->get_cart() as $cart_item ){
                $cart_item['data']->set_price( $cart_item['data']->get_regular_price()/* / 2*/ );
                //(new Helper())->dump($cart_item['data']);
            }
        }
    }

    //(new Helper())->dump($wc_cart->get_cart());
}

/*add_action( 'woocommerce_before_checkout_form', 'action_function_name_9574' );
function action_function_name_9574( $wc_cart ){
    //(new Helper())->dump($wc_cart->get_cart()); die;

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        if($discount == 50) {
            foreach ( $wc_cart as $cart_item ){
                //$cart_item['data']->set_price( $cart_item['data']->get_regular_price() / 2 );
                //(new Helper())->dump($cart_item['data']);
            }
        }
    }

    //(new Helper())->dump($wc_cart); die;
}*/

/*add_action( 'woocommerce_checkout_order_created', 'action_function_name_1276' );
function action_function_name_1276( $order ){
    (new Helper())->dump($order); die;
}*/

add_action( 'woocommerce_after_checkout_process', 'action_function_name_5884' );
function action_function_name_5884(){

    $wc_cart = WC()->cart->get_cart();
    //(new Helper())->dump($wc_cart); die;

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        if($discount == 50) {
            foreach ( $wc_cart as $cart_item ){
                //$cart_item['data']->set_price( $cart_item['data']->get_regular_price() / 2 );
            }
        }
    }
}



/*add_filter( 'woocommerce_add_cart_item', 'filter_function_name_2924', 10, 2 );

function filter_function_name_2924( $array_merge, $cart_item_key ){

    $array_merge['data']->set_price('1000');
    //(new Helper())->dump($array_merge); die;
    return $array_merge;
}*/

/*add_action( 'woocommerce_checkout_create_order', 'action_function_name_8842', 10, 2 );
function action_function_name_8842( $order, $data ){


    $items = $order->get_items(['line_item']);

    foreach($items as $item) {
        echo $item->get_total();
        $order_id = $item->get_order_id();
        echo $order_id;
        //wc_update_order_item_meta( $order_id, '_line_total', 30 );
        //wc_update_order_item_meta( $order_id, '_line_total', 555 );
    }

    //(new Helper())->dump($order->get_items()); die;
}*/

add_action( 'woocommerce_add_order_item_meta', 'order_item_meta', 10, 2 );

// $item_id – order item ID
// $cart_item[ 'product_id' ] – associated product ID (obviously)
function order_item_meta( $item_id, $cart_item ) {

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        if($discount == 50) {
            $q = $cart_item[ 'quantity' ];
            $p = $cart_item[ 'line_subtotal' ];// / 2;
            if($q > 1) {
                $p = $p / 2;
            }

            wc_update_order_item_meta( $item_id, '_line_subtotal', $p );
            wc_update_order_item_meta( $item_id, '_line_total', ($p * $q) );
        }
    }
}

/*add_filter( 'woocommerce_add_cart_item', 'my_add_cart_item', 10, 1 );
function my_add_cart_item($cart_item) {
    $cart_item['data']->set_price('1000');
}*/

//add_action('woocommerce_after_calculate_totals', 'set_custom_price');
/*function set_custom_price($cart_obj) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    $getcart = WC()->cart->get_cart();


    $sub_total = 0;
    foreach ( $getcart as $cart_item_key => $cart_item ) {
        //(new Helper())->dump( $cart_item['data']);//die;
        //(new Helper())->dump( $cart_item['quantity']);
        $price_personal = $cart_item['data']->get_regular_price() * $cart_item['quantity'];
        $sub_total += $price_personal;
        //$cart_item['data']->set_price( 100 );
        //(new Helper())->dump($cart_item['data']);
    }

    WC()->cart->set_total( $sub_total / 2 );
}*/

/*add_filter( 'woocommerce_add_cart_item_data', 'filter_function_name_6346', 10, 4 );

function filter_function_name_6346( $cart_item_data, $product_id, $variation_id, $quantity ){
    // filter...
    //(new Helper())->dump($cart_item_data); die;
    $f = fopen('woocommerce_add_cart_item_data', 'w+');
    //fwrite($f, $cart_item_data['data']->get_regular_price());

    return $cart_item_data;
}*/

/*add_action( 'woocommerce_before_add_to_cart_form', 'my_woocommerce_before_add_to_cart_form' );

function my_woocommerce_before_add_to_cart_form() {

    $getcart = WC()->cart->get_cart();

    $f = fopen('my_woocommerce_before_add_to_cart_form', 'w+');
    fwrite($f, 'test');
}*/

/*add_action('woocommerce_format_sale_price', 'ss_format_sale_price');

function ss_format_sale_price( $regular_price, $sale_price ) {

    $sale_price = (new WooHelper())->getPersonalSalePrice($regular_price, $sale_price);

    $price = '<del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
    return apply_filters( 'woocommerce_format_sale_price', $price, $regular_price, $sale_price );
}*/


// Добавляем значение сэкономленных процентов рядом с ценой у товаров
/*add_filter( 'woocommerce_sale_price_html', 'woocommerce_custom_sales_price', 10, 2 );

function woocommerce_custom_sales_price( $price, $product ) {
    $percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
    return $price . sprintf( __(' Экономия %s', 'woocommerce' ), $percentage . '%' );
}*/
/*  /Персональная скидка */

// получаем массив всех вложенных категорий
function hml_get_category_gender_line( $cat_parent ) {
    // get_term_children() accepts integer ID only
    $line = get_term_children( (int) $cat_parent, 'product_cat');
    $line[] = $cat_parent;
    return $line;
}


// удаляем текущий вывод цены
//
//add_filter( 'woocommerce_get_price_html', 'hide_all_wc_prices', 100, 2);

// заменяем нашим фильтром
//add_filter( 'woocommerce_get_price_html', 'custom_price_html', 100, 2 );

function custom_price_html( $price, $product ){

    //global $product;
    //(new Helper())->dump((new WooHelper())->getVariation($product)); die;
    //echo $price; //die;

    $discount_price = 0;

    $is_variation_price = false;

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();

        $discount = (int)(new Crm())->getUserDiscount($current_user->user_email);

        if($discount) {

            $p = $discount / 100;

            if(null != get_post_meta( get_the_ID(), '_price', true)) {
                $is_variation_price = true;
                $regular_price = str_replace([' ', ' '], '', get_post_meta( get_the_ID(), '_price', true));

            } else {
                $regular_price = str_replace([' ', ' '], '', get_post_meta( get_the_ID(), '_regular_price', true));
            }

            if(null != get_post_meta( get_the_ID(), '_sale_price', true)) {
                $regular_price = str_replace([' ', ' '], '', get_post_meta( get_the_ID(), '_sale_price', true));
                echo $regular_price;
            }

            //(new Helper())->dump( $product->get_variation_prices( false ) );

            //echo get_the_ID();
            //echo get_post_meta( get_the_ID(), '_price', true); //die;

            $discount_price = $regular_price - $regular_price * $p; // 0.05 - это 5%

            $s = $regular_price - $discount_price;
        }

        if($discount_price) {
            $hidden = $is_variation_price ? '' : '';
            /*$price .= '<div class="discount-personal" '.$hidden.'><div class="discount-value">Ваша персональная скидка - <span>'.$discount.'</span>%</div>';
            $price .= '<p class="price">Стоимость с учётом Вашей скидки ' . wc_price( $discount_price ). '</p>';
            $price .= '<p class="price s">Ваша экономия ' . wc_price( $s ). '</p></div>';*/
        }
    } else {
        $price .= '<span class="symbol">' . sprintf(get_woocommerce_currency_symbol() ) . '</span>';
    }

    return apply_filters( 'woocommerce_get_price', $price );
}



/**
 * Заверши свой образ ()
 *
 * @param $id
 * @param int $limit
 * @return array|int[]|WP_Post[]
 */

//add_action('init','get_featured_custom');

function get_featured_custom($product, $limit = 4) {

    global $woocommerce;
    global $product;

    $id = $product->get_id();
    $tags = $product->get_tag_ids();

    $h = new Helper();
    $wh = new WooHelper();

    // Related products are found from category and tag
    $tags_array = [];
    //$cats_array = array(0);

    // получим родительские категории товара
    $args = array( 'taxonomy' => 'product_cat',);
    $terms = wp_get_post_terms($id,'product_cat', $args);

    //$h->dump($terms);

    // текущая категория товара
    $this_category = $terms[count($terms) - 1]->slug;

    // категория товара на уровень выше
    $parent_category = $terms[count($terms) - 2]->slug;

    // метки товара
    $terms = wp_get_post_terms($id, 'product_tag');

    foreach ( $terms as $term ) {
        $tags_array[] = $term->term_id;
    }

    //$h->dump($tags_array);

    //echo $this_category;

    // Meta query
    $meta_query = array();
    $meta_query[] = $woocommerce->query->visibility_meta_query();
    $meta_query[] = $woocommerce->query->stock_status_meta_query();

    // товары для завершения образа
    $related_posts = [];

    // массив соответствий категорий для завершения образа
    $arr_related_products = $wh->getRelatedProducts();

    if(isset($arr_related_products[$this_category])) {
        // если есть описание для текущей категории
        $cats_array = $arr_related_products[$this_category];
        $related_posts = getFeaturedItemQuery($cats_array, $meta_query, $tags_array);
    } else {
        // если есть описание для родительской категории
        if(isset($arr_related_products[$parent_category])) {
            $cats_array = $arr_related_products[$parent_category];
            $related_posts = getFeaturedItemQuery($cats_array, $meta_query, $tags_array);
        }
    }

    // исключается вывод этого же товара
    //$related_posts = array_diff( $related_posts, array( $id ));

    $related_posts = array_slice($related_posts, 0, $limit);

    return $related_posts;
}


/**
 * Получим массив товаров для завершения образа
 *
 * @param $arr
 * @param $meta_query
 * @return array|int[]|WP_Post[]
 */
function getFeaturedItemQuery($arr, $meta_query, $tags_array) {

    foreach($arr as $cat_name) {
        if(!isset($related_posts)) {
            $related_posts = getFeaturedItem($meta_query, $cat_name, $tags_array);

            if(empty($related_posts)) {
                $related_posts = getFeaturedItem($meta_query, $cat_name, []);
            }
        } else {
            $a = getFeaturedItem($meta_query, $cat_name, $tags_array);

            if(empty($a)) {
                $a = getFeaturedItem($meta_query, $cat_name, []);
            }

            $related_posts = array_merge($related_posts, $a);
        }
    }

    return $related_posts;
}

/**
 * Получим товар для блока Заверши образ (случайный из сопутствующей категории)
 *
 * @param $meta_query
 * @param $slug
 * @return int[]|WP_Post[]
 */
function getFeaturedItem($meta_query, $slug, $tags_array) {

    $tax_query = [
        [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $slug,
        ]
    ];

    // если у товара есть метки, то подберем товары с такими же метками
    if(!empty($tags_array)) {
        $tax_query['relation'] =  'AND';

        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field' => 'id',
            'terms' => $tags_array
        ];
    }

    $related_posts = get_posts( apply_filters('woocommerce_product_related_posts', array(
        'orderby' => 'rand',
        'posts_per_page' => 1,//$limit + 1,
        'post_type' => 'product',
        'fields' => 'ids',
        'meta_query' => $meta_query,
        'tax_query' => $tax_query
    ) ) );

    return $related_posts;
}

/**
 * Похожие товары (берутся из той же подкатегории, что и товар + с такими же метками (независимо от категории))
 *
 * @param $id
 * @param int $limit
 * @return array|int[]|WP_Post[]
 */
add_action('init','get_related_custom');
function get_related_custom($id, $limit = 4 ) {

    global $woocommerce;

    // Related products are found from category and tag
    $tags_array = array(0);
    $cats_array = array(0);

    // Get tags
    $terms = wp_get_post_terms($id, 'product_tag');

    foreach ( $terms as $term ) {
        $tags_array[] = $term->term_id;
    }

    // Get categories (removed by NerdyMind)
    $terms = wp_get_post_terms($id, 'product_cat');

    foreach ( $terms as $term ) {
        $cats_array[] = $term->term_id;
    }

    // Don't bother if none are set
    if ( sizeof($cats_array)==1 && sizeof($tags_array)==1 ) {
        //return array();
    }

    // Meta query
    $meta_query = array();
    $meta_query[] = $woocommerce->query->visibility_meta_query();
    $meta_query[] = $woocommerce->query->stock_status_meta_query();

    // Get the posts
    $related_posts = get_posts( apply_filters('woocommerce_product_related_posts', array(
        'orderby' => 'rand',
        'posts_per_page' => $limit + 1,
        'post_type' => 'product',
        'fields' => 'ids',
        'meta_query' => $meta_query,
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $cats_array[sizeof($cats_array) - 1] // текущая подкатегория
            ),
            array(
                'taxonomy' => 'product_tag',
                'field' => 'id',
                'terms' => $tags_array
            )
        )
    ) ) );

    shuffle($related_posts);

    // исключается вывод этого же товара
    $related_posts = array_diff( $related_posts, array( $id ));

    $related_posts = array_slice($related_posts, 0, $limit);

    return $related_posts;
}
/*************************************************************************************************************/
/*************************************************************************************************************/


/* цену вариации в карточке товара поднял выше атрибутов */

remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10);
add_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation', 20);

/* /цену вариации в карточке товара поднял выше атрибутов */

add_filter( 'woocommerce_variable_sale_price_html', 'my_variation_price_format', 10, 2 );

//add_filter( 'woocommerce_variable_price_html', 'my_variation_price_format', 10, 2 );

function my_variation_price_format( $price, $product ) {

// Main Price
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    //$price = $prices[0] !== $prices[1] ? sprintf( __( 'от %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    $price = $prices[0] !== $prices[1] ? '' : wc_price( $prices[0] );

// Sale Price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    if ( $price !== $saleprice ) {
        //$price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}

//add_filter( 'woocommerce_product_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
//add_filter( 'woocommerce_product_variation_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );

/*function custom_dynamic_sale_price( $sale_price, $product ) {
    $rate = 0.8;
    //if( empty($sale_price) || $sale_price == 0 )
        return $sale_price+100;//$product->get_regular_price() * $rate;
    //else
        //return $sale_price;
};*/

//add_filter( 'woocommerce_get_variation_sale_price', 'filter_function_name_8880', 10, 4 );

/*function filter_function_name_8880( $price, $product){

    $price = 100;

    return $price;
}*/

/*add_action( 'woocommerce_single_variation', 'action_function_name_7179' );
function action_function_name_7179(){
    //echo 'test';
}*/

add_filter('gettext', 'translate_text');
add_filter('ngettext', 'translate_text');

function translate_text($translated) {
    $translated = str_ireplace('Товары с меткой &ldquo;%s&rdquo;', '%s', $translated);
    return $translated;
}

add_action('woocommerce_checkout_after_terms_and_conditions', 'my_order_fields', 99);
function my_order_fields($checkout) {
    echo '<div id="custom_checkout_field">';

    echo '<label for="agreement" style="display: inline-block"><input type="checkbox" class="input-checkbox" name="agreement" id="agreement" required /> Настоящим я даю свое согласие ООО "Дом Луи" на обработку персональных данных и подтверждаю принятие условий <a href="/offerta/" target="_blank">Публичной оферты</a></label>';

    echo '</div>';
}

add_action('woocommerce_checkout_process', 'my_checkout_field_process');

function my_checkout_field_process() {
  // какая логика
  if ( !$_POST['agreement'] ) {
      wc_add_notice('Вы должны принять условия и положения, прежде чем оформить заказ', 'error' );
  }

}

// не проверять артикул на уникальность
add_filter( 'wc_product_has_unique_sku', '__return_false', PHP_INT_MAX );

// удаляем доставку, если количество вещей в корзине больше 5
add_filter( 'woocommerce_package_rates', 'my_remove_shipping_method', 20, 2 );

function my_remove_shipping_method( $rates, $package ) {

    if ( WC()->cart->get_cart_contents_count() > (new WooHelper())::CART_CONTENTS_COUNT_MAX ) {
        //unset( $rates[ 'flat_rate:5' ] );
        foreach($rates as $rate) {
            if ($rate->id != 'wc_pickup_store') {
                unset ($rates[$rate->id]);
            }
        }
    }

    return $rates;
}
/*******************/
/* /My Woocommerce */
/*******************/


/* добавлены поля в профиль пользователя */
$tag             = 'woocommerce_save_account_details';
$function_to_add = 'my_save_account';
$priority        = 10;
$accepted_args   = 1;
add_action( $tag, $function_to_add, $priority, $accepted_args );

function my_save_account($user_id) {

    $subscribe  = !empty( $_POST[ 'subscribe' ] ) ? true : false;
    $sms  = !empty( $_POST[ 'sms' ] ) ? true : false;

    update_user_meta($user_id, 'subscribe', (int)$subscribe);
    update_user_meta($user_id, 'sms', (int)$sms);
    update_user_meta($user_id, 'born_date', $_POST[ 'born_date' ]);
    update_user_meta($user_id, 'gender', $_POST[ 'gender' ]);
    update_user_meta($user_id, 'phone', $_POST[ 'phone' ]);
    update_user_meta($user_id, 'user_card', $_POST[ 'user_card' ]);

    (new Crm())->setSubscribeStatus('subscribe', (int)$subscribe);
    (new Crm())->setSubscribeStatus('sms', (int)$sms);
}

function my_jpeg_quality($arg)
{
    return (int)100;
}
add_filter('jpeg_quality', 'my_jpeg_quality');

function createuser() {
    //$user_id = register_new_user( 'vkurlenko2', 'vkurlenko@ya2.ru' );
}

/*add_action( 'user_register', '____action_function_name' );
function ____action_function_name( $user_id ) {
    echo $user_id; die;
}*/

// предварительная проверка поля
/*add_filter( 'registration_errors', 'my_validate_user_data' );

function my_validate_user_data( $errors ){

    (new Helper())->writeToFile('my_validate_user_data', 'my_validate_user_data');

    $errors->add('empty_user_sex', 'Пол обязательно должен быть указан!' );
    //var_dump($_POST); die;

    if( empty($_POST['user_sex']) )
        $errors->add('empty_user_sex', 'Пол обязательно должен быть указан!' );
    elseif( ! in_array($_POST['user_sex'], array('male','female')) )
        $errors->add('invalid_user_sex', 'Пол указан неверно!' );

    return $errors;
}*/


/**
 * Запись/обновление данных пользователя в CRM при обновлении профиля
 *
 * @param $meta
 * @param $user
 * @param $update
 * @return mixed
 */
add_filter( 'insert_user_meta', 'my_user_registration_meta', 10, 3 );

function my_user_registration_meta($meta, $user, $update ) {

    if( $update ) {
        (new Crm())->insertUserToCRM($meta, $user, $_POST);
    }

    //$meta['user_sex'] = $_POST['user_sex']; // $_POST['user_sex'] проверена заранее...

    return $meta;
}

/**
 * Регистрация клиента в CRM сразу после оформления первого заказ
 * TODO проверить $meta и добавить $_POST
 */
/*add_action( 'user_register', 'user_register_to_crm' );
function user_register_to_crm( $user_id ) {

    $user = get_user_by( 'ID', $user_id );
    $meta = get_userdata( $user_id );

    (new Crm())->insertUserToCRM($meta, $user, $_POST);
}*/

/**
 * Добавляем номер карты лояльности в заказ
 *
 * my_custom_checkout_field - добаляем номер карты в профиль клиента
 * my_custom_checkout_field_update_order_meta - пишем номер карты в заказ
 * my_custom_checkout_field_display_admin_order_meta - показываем номер карты в деталях заказа
 * my_custom_order_meta_keys - добавляем номер карты в email
*/
add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );
function my_custom_checkout_field( $checkout ) {

    echo '<div id="my_custom_checkout_field" style="display: none"><!--<h2>' . __('Номер карты лояльности') . '</h2>-->';

    woocommerce_form_field( 'user_card', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        //'label'         => __('Fill in this field'),
        //'placeholder'   => __('Enter something'),
    //), $checkout->get_value( 'user_card' ));
    ), (new Crm())->getUserCard());

    echo '</div>';
}

add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['user_card'] ) ) {
        //update_post_meta( $order_id, 'user_card', sanitize_text_field( $_POST['user_card'] ) );
        update_post_meta( $order_id, 'user_card', sanitize_text_field( (new Crm())->getUserCard() ) );
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Номер карты лояльности').':</strong> ' . get_post_meta( $order->get_id(), 'user_card', true ) . '</p>';
    //echo '<p><strong>'.__('Номер карты лояльности').':</strong> ' . (new Crm())->getUserCard() . '</p>';
}

add_filter('woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys');
function my_custom_order_meta_keys( $keys ) {
    $keys[] = 'user_card'; // This will look for a custom field called 'Tracking Code' and add it to emails
    return $keys;
}
/** */

/**
 * Теги микроданных
 */
function wh_doctype_opengraph($output) {
    return $output . '
xmlns:og="http://opengraphprotocol.org/schema/"
xmlns:fb="http://www.facebook.com/2008/fbml"';
}

add_filter('language_attributes', 'wh_doctype_opengraph');
function wh_fb_opengraph()
{
    global $post;
    if (is_product())
    {
        $product = wc_get_product( $post->ID );
        $price = $product ? $product->get_variation_regular_price( 'min' ) : '';
        $category = (new Helper())->getProductCategoriesById($post->ID);
        $img_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'woocommerce_single_image_width'); //replace it with your desired size
        ?>
        <meta property="og:type" content="product" />
        <meta property="og:title" content="<?= get_the_title($post->ID); ?>"/>
        <meta property="og:description" content="<?= $product->get_description(); ?>" />
        <meta property="og:url" content="<?= get_the_permalink($post->ID); ?>" />
        <meta property="product:brand" content="Lion Of Porches">
        <meta property="product:availability" content="<?= $product->get_stock_status();?>" />
        <meta property="product:price:amount" content="<?= $price; ?>" />
        <meta property="product:price:currency" content="RUB" />
        <meta property="product:retailer_item_id" content="<?= $post->ID; ?>" />
        <meta property="product:item_group_id" content="<?= $category; ?>">
        <meta property="og:locale" content="ru_RU" />
        <meta property="og:site_name" content="LionOfPorchesRus" />
        <meta property="og:image" content="<?= $img_url[0]; ?>"/>
        <?php
    }
    //for product cat page
    else if (is_product_category())
    {
        $term = get_queried_object();
        $img_src = wp_get_attachment_url(get_term_meta($term->term_id, 'thumbnail_id', true));
        if (empty($img_src))
        {
            //$img_src = get_site_url() . '/wp-content/uploads/myproductcat.jpg'; //replace it with your static image
        }
        ?>
        <meta property="og:type" content="object" />
        <meta property="og:title" content="<?= $term->name; ?>" />
        <meta property="og:url" content="<?= get_term_link($term->term_id, 'product_cat'); ?>" />
        <meta property="og:image" content="<?= $img_src; ?>" />
        <?php
    }
    else
    {
        return;
    }
}

add_action('wp_head', 'wh_fb_opengraph', 5);


/**
 * Делаем поле Почтовый индекс необязательными
*/
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );
function custom_override_default_address_fields( $address_fields ) {
    $address_fields['postcode']['required'] = false; //почтовый индекс

    return $address_fields;
}

/**
 * Уберем поля адреса клиента при выборе самовывоза из магазина
 */
add_action( 'woocommerce_after_checkout_form', 'bbloomer_disable_shipping_local_pickup' );

function bbloomer_disable_shipping_local_pickup( $available_gateways ) {
    global $woocommerce;

    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping_no_ajax = $chosen_methods[0];
    if ( 0 === strpos( $chosen_shipping_no_ajax, 'wc_pickup_store' ) ) {
        ?>
        <script type="text/javascript">
            jQuery('.address-field').fadeOut();
        </script>
        <?php
    }
    ?>
    <script type="text/javascript">
        jQuery('form.checkout').on('change','input[name^="shipping_method"]',function() {
            var val = jQuery( this ).val();
            if (val.match("^wc_pickup_store")) {
                jQuery('.address-field').fadeOut();
            } else {
                jQuery('.address-field').fadeIn();
            }
        });
    </script>
    <?php
}
?>
