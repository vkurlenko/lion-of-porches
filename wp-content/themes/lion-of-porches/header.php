<?php
/**
 * Шаблон шапки (header.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
//include 'Helper.php';
//include 'WooHelper.php';
//include 'Crm.php';
$helper = new Helper();
$woo = new WooHelper();
$crm = new Crm();

/*$crm->importUsers();

die;*/


?>
<!DOCTYPE html>
<html <?php language_attributes(); // вывод атрибутов языка ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); // кодировка ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/favicon.png">
    <?php /* RSS и всякое */ ?>
    <link rel="alternate" type="application/rdf+xml" title="RDF mapping" href="<?php bloginfo('rdf_url'); ?>">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php bloginfo('rss_url'); ?>">
    <link rel="alternate" type="application/rss+xml" title="Comments RSS" href="<?php bloginfo('comments_rss2_url'); ?>">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

    <link rel="stylesheet" href="/wp-content/themes/lion-of-porches/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@1,700&display=swap" rel="stylesheet">
    <?php /* Все скрипты и стили теперь подключаются в functions.php */ ?>

    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <?php wp_head(); // необходимо для работы плагинов и функционала ?>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(65553346, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true,
            ecommerce:"dataLayer"
        });
    </script>

    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
    </script>

    <script type="text/javascript">
        <?php
        $url = explode('/', $_SERVER['REQUEST_URI']);

        if(in_array('product', $url)) {

            global $post;
            $id = $post->ID;

            $product = wc_get_product( $id );
            $price = $product ? $product->get_variation_regular_price( 'min' ) : '';
            $category = (new Helper())->getProductCategoriesById($id);
            ?>
            window.dataLayer.push({
                "ecommerce": {
                    "detail": {
                        "products": [
                            {
                                "id": "<?=$product->get_sku();?>",
                                "name": "<?=$product->get_name()?>",
                                "price": <?=$price?>,
                                "brand": "Lion of Porches",
                                "category": "<?=$category?>",
                                //"variant": "Красный цвет"
                            }
                        ]
                    }
                }
            });

            function addPush() {
                dataLayer.push({
                    "ecommerce": {
                        "add": {
                            "products": [
                                {
                                    "id": "<?=$product->get_sku();?>",
                                    "name": "<?=$product->get_name()?>",
                                    "price": <?=$price?>,
                                    "brand": "Lion of Porches",
                                    "category": "<?=$category?>",
                                    "quantity": 1
                                }
                            ]
                        }
                    }
                });
            }
        <?php
        } elseif(in_array('order-received', $url)) {

            $index = array_search('order-received', $url);
            $the_order = $url[$index + 1];

            if($the_order) {
                $order = wc_get_order($the_order);
                ?>
                dataLayer.push({
                    "ecommerce": {
                        "purchase": {
                            "actionField": {
                                "id" : "<?=$the_order?>"
                            },
                            "products": [
                                <?=(new Helper())->getOrderItemsForYM($order)?>
                            ]
                        }
                    }
                });

                <?php
                }
            }
        ?>
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/65553346" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '663993440893072');
        fbq('track', 'PageView');


        <?php
        $arr = [
            'checkout' => 'InitiateCheckout', // Начало оформления заказа
            //'' => 'AddToCart',  // Добавление в корзину
            'product' => 'CustomizeProduct', // Персонализация товара
            'order-received' => 'Purchase', // Покупка // fbq('track', 'Purchase', {value: 0.00, currency: 'RUB'});
            //'' => 'Search' // Поиск
        ];

        $url = explode('/', $_SERVER['REQUEST_URI']);

        $track = '';

        if(in_array('order-received', $url)) {
            $track = "'".$arr['order-received']."'";
            $index = array_search('order-received', $url);
            $the_order = $url[$index + 1];

            if($the_order) {
                $order = wc_get_order( $the_order );
                $total = $order->get_total();

                $track .= ", {value: ".$total.", currency: 'RUB'}";
            }
        } elseif(in_array('checkout', $url)) {
            $track = "'".$arr['checkout']."'";
        } elseif(in_array('product', $url)) {
            $track = "'".$arr['product']."'";

        global $post;
        //global $product;
        $id = $post->ID;

        $product = wc_get_product( $id );
        $value = $product ? $product->get_variation_regular_price( 'min' ) : '0';

        ?>
        fbq('track', 'ViewContent', {
            content_ids: ['<?=$id?>'],
            content_type: 'product',
            value: <?=$value?>,
            currency: 'RUB'
        });
        <?
        }

        if($track) {
            ?>fbq('track', <?=$track?>);<?php
        }
        ?>

    </script>
    <noscript>
        <img height="1" width="1"
             src="https://www.facebook.com/tr?id=663993440893072&ev=PageView
&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
</head>

<body <?php body_class(); // все классы для body ?>>
<header>
    <div class="container">
        <div class="row">

            <!-- top-left-block -->
            <div class="col-md-5 col-sm-4 col-xs-5 header-left hidden-xs" style="/*background: #ccc*/">

                <!-- меню категорий товаров -->
                <?php
                $product_categories = $helper->getTopCategory();
                if($product_categories):?>
                    <div class="main-menu">
                        <ul>
                            <?php
                            foreach ( $product_categories as $product_category ):?>
                                <li><a class="btn-alt" data-subcategory="<?=$product_category->slug?>"  href="<?=get_term_link($product_category) ?>"><?=$product_category->name?></a>

                                    <div class="container-fluid sub-menu ">
                                        <!-- 121 38 39 40 -->
                                        <!-- woman -->
                                        <!--<div class="<?/*=$product_category->slug*/?> hidden container">-->
                                        <div class="<?=$product_category->slug?> hidden container">
                                            <?php
                                            $helper->getSabCategoryTree($product_category->term_id);
                                            ?>
                                        </div>
                                        <!-- /woman -->

                                        <!-- man -->
                                        <!--<div class="for-man ">
                                            <?php
/*                                            $helper->getSabCategoryTree(38);
                                            */?>
                                        </div>-->
                                        <!-- /man -->

                                    </div>
                                </li>
                            <?php
                            //break;
                            endforeach;

                            if($woo->isNewArrivalTagProducts()):
                            ?>
                            <li class="new-arrival">
                                <a class="btn-alt" data-subcategory="new-arrival" href="javascript: return false;">New arrival</a>
                                <div class="container-fluid sub-menu ">

                                    <div class="new-arrival hidden container">
                                        <ul>
                                            <li class="cat-item">
                                                <a href="/shop/new-arrival/?tag=woman">Женщины</a>
                                                <ul class="children">
                                                    <li class="cat-item"></li>
                                                </ul>
                                            </li>
                                            <li class="cat-item">
                                                <a href="/shop/new-arrival/?tag=man">Мужчины</a>
                                                <ul class="children">
                                                    <li class="cat-item"></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </li>
                            <?php
                            endif;
                            ?>
                        </ul>
                    </div>
                <?
                endif;
                ?>
                <!-- /меню категорий товаров -->

                <!-- форма поиска -->
                <div class="open-search">
                    <?php
                    //get_search_form();
                    ?>
                    <?php if ( function_exists( 'aws_get_search_form' ) ) { aws_get_search_form(); } ?>
                    <?php /*echo do_shortcode('[yith_woocommerce_ajax_search]');*/?>
                    <?php /*echo do_shortcode('[wcas-search-form]'); */?>
                </div>
                <!-- /форма поиска -->
            </div>
            <!-- /top-left-block -->

            <!-- logo -->
            <div class="col-md-2 col-sm-4 col-xs-6 logo">
                <h1>
                    <a href="/"><img class="logo-red hidden-xs" src="/wp-content/themes/lion-of-porches/img/lion-of-porches-red.png"><img class="logo-blue visible-xs" src="/wp-content/themes/lion-of-porches/img/lion-of-porches-blue.png"></a>
                </h1>
            </div>
            <!-- /logo -->

            <!-- top-right-block -->
            <div class="col-md-5  col-sm-4  col-xs-6 header-right"  style="/*background: #00ff00*/">
                <div class="top-menu-1 hidden-xs">
                    <?
                    $args = array(
                        //'theme_location'  => , // область темы
                        'menu'            => 'top-menu-1', // какое меню нужно вставить (по порядку: id, ярлык, имя)
                        /*'container'       => 'div', // блок, в который нужно поместить меню, укажите false, чтобы не помещать в блок
                        'container_class' => 'menu-{menu slug}-container', // css-класс блока
                        'container_id'    => , // id блока
                        'menu_class'      => 'menu', // css-класс меню
                        'menu_id'         => , // id меню
                        'echo'            => true, // вывести или записать в переменную
                        'fallback_cb'     => 'wp_page_menu', // какую функцию использовать если меню не существует, укажите false, чтобы не использовать ничего
                        'before'          => , // текст или html-код, который нужно вставить перед каждым <a>
                        'after'           => , // после </a>
                        'link_before'     => , // текст перед анкором ссылки
                        'link_after'      => , // после анкора и перед </a>
                        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>', // HTML-шаблон
                        'depth'           => 0 // количество уровней вложенности*/
                    );
                    //wp_nav_menu($args);?>

                    <!--<ul>
                        <li><a href="#">Доставка</a></li><li><a href="#">О компании</a> </li>
                    </ul>-->
                </div>
                <div class="top-menu-2 hidden-xs">

                    <?
                    //$login = '<a href="'.get_permalink( get_option('woocommerce_myaccount_page_id')).'">'.(is_user_logged_in() ? 'Личный кабинет' : 'Вход').'</a>';
                    $login = '<a href="'.get_permalink( get_option('woocommerce_myaccount_page_id')).'">'.(is_user_logged_in() ? 'Личный кабинет' : 'Личный кабинет').'</a>';

                    $args = array(
                        //'theme_location'  => , // область темы
                        'menu'            => 'top-menu-2', // какое меню нужно вставить (по порядку: id, ярлык, имя)
                        /*'container'       => 'div', // блок, в который нужно поместить меню, укажите false, чтобы не помещать в блок
                        'container_class' => 'menu-{menu slug}-container', // css-класс блока
                        'container_id'    => , // id блока
                        'menu_class'      => 'menu', // css-класс меню
                        'menu_id'         => , // id меню
                        'echo'            => true, // вывести или записать в переменную
                        'fallback_cb'     => 'wp_page_menu', // какую функцию использовать если меню не существует, укажите false, чтобы не использовать ничего
                        'before'          => , // текст или html-код, который нужно вставить перед каждым <a>
                        'after'           => , // после </a>
                        'link_before'     => , // текст перед анкором ссылки
                        'link_after'      => , // после анкора и перед </a>
                        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>', // HTML-шаблон
                        'depth'           => 0 // количество уровней вложенности*/
                        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s<li class="menu-item">'.$login.'</li><li><a class="cart-link" href="'.wc_get_cart_url().'"><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span class="count">('.count(WC()->cart->cart_contents).')</span></a></li></ul>', // HTML-шаблон
                    );
                    wp_nav_menu($args);?>


                    <!--<ul>
                        <li><a href="/shop/">Доставка</a></li><li><a href="/shop/">О бренде</a></li><li><a href="/shop/">Каталоги</a></li><li><a href="/forpartners/">Партнерам</a></li><li><?php /*get_template_part( 'parts/login-link');*/?></li><li><a class="cart-link" href="<?/*=wc_get_cart_url();*/?>"><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span class="count">(<?/*=count(WC()->cart->cart_contents)*/?>)</span></a></li>
                    </ul>-->
                </div>

                <div class="cart-mobile visible-xs">
                    <div>
                        <a class="cart-link" href="<?=wc_get_cart_url();?>" ><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span class="count">(<?=count(WC()->cart->cart_contents)?>)</span></a>
                        <div class='threebar hamburger'>
                            <div class='bar'></div>
                            <div class='bar'></div>
                            <div class='bar'></div>
                        </div>
                    </div>
                </div>

                <?php get_template_part( 'parts/person-info');?>

            </div>
            <!-- /top-right-block -->
        </div>

        <!-- mobile menu -->
        <?php get_template_part( 'parts/main-menu-mobile');?>
        <!-- /mobile menu -->
    </div>
</header>

    <div style="font-size: 10px; font-family: arial">
    <?php
    //$woo->createVarProductsFromFile(); die;
    /*include 'Crm.php';

    $crm = new Crm();

    $crm->createUser();*/
    ?>
    </div>



