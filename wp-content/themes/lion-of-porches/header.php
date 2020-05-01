<?php
/**
 * Шаблон шапки (header.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
//include 'Helper.php';
$helper = new Helper();
?>
<!DOCTYPE html>
<html <?php language_attributes(); // вывод атрибутов языка ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); // кодировка ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</head>

<body <?php body_class(); // все классы для body ?>>
<header>
    <div class="container">
        <div class="row">

            <!-- top-left-block -->
            <div class="col-md-5 col-sm-5 col-xs-5 header-left hidden-xs" style="/*background: #ccc*/">
                <?php
                $product_categories = $helper->getTopCategory();
                if($product_categories):?>
                    <div class="main-menu">
                        <ul>
                            <?php
                            foreach ( $product_categories as $product_category ):?>
                                <li><a class="btn-alt" href="<?= get_term_link($product_category) ?>"><?=$product_category->name?></a></li>
                            <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>
                <?
                endif;
                ?>

                <!--<div class="main-menu">
                    <ul>
                        <li><a href="/catalog/">Женщины</a></li><li><a href="/catalog/">Мужчины</a></li><li><a href="/catalog/">Девочки</a></li><li><a href="/catalog/">Мальчики</a></li>
                    </ul>
                </div>-->

                <div class="open-search">
                    <form class="form-inline">
                        <!--<input class="form-control" type="text" value="" placeholder="поиск">
                        <i class="fa fa-search" aria-hidden="true"></i>-->

                        <div class="input-group">
                            <input type="text" class="form-control" id="" placeholder="поиск">
                            <div class="input-group-addon"><button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button></div>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /top-left-block -->

            <!-- logo -->
            <div class="col-md-2 col-sm-2 col-xs-6 logo">
                <h1>
                    <a href="/"><img class="logo-red hidden-xs" src="/wp-content/themes/lion-of-porches/img/lion-of-porches.png"><img class="logo-blue visible-xs" src="/wp-content/themes/lion-of-porches/img/lion-of-porches-blue.webp"></a>
                </h1>
            </div>
            <!-- /logo -->

            <!-- top-right-block -->
            <div class="col-md-5  col-sm-5  col-xs-6 header-right"  style="/*background: #00ff00*/">
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
                    wp_nav_menu($args);?>

                    <!--<ul>
                        <li><a href="#">Доставка</a></li><li><a href="#">О компании</a> </li>
                    </ul>-->
                </div>
                <div class="top-menu-2 hidden-xs">

                    <ul>
                        <li><a href="/catalog/">Каталоги</a></li><li><a href="/forpartners/">Партнерам</a></li><li><a href="#">Войти</a></li><li><a class="cart-link" href="#"><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span class="count">(0)</span></a></li>
                    </ul>
                </div>

                <div class="cart-mobile visible-xs">
                    <div>
                        <a class="cart-link" href="#" ><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span class="count">(0)</span></a>
                        <div class='threebar hamburger'>
                            <div class='bar'></div>
                            <div class='bar'></div>
                            <div class='bar'></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /top-right-block -->
        </div>

        <!-- mobile menu -->
        <?php get_template_part( 'parts/main-menu-mobile');?>
        <!-- /mobile menu -->
    </div>
</header>


