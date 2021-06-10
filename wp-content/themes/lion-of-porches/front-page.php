<?php
/**
 * Template Name: FrontPage (front-page.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php

$product_categories = $helper->getTopCategory();
?>
<section>
    <div class="main">

        <!-- big banner -->
        <div class="container-fluid">
            <div class="row">
                <div class="big-banner">
                    <!--<img class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/promoOPT3.jpg">
                    <img class="visible-xs" src="/wp-content/themes/lion-of-porches/img/promoOMEN2.jpg">-->

                    <?php
                    $full_banner = $helper->wp_get_post_by_slug( 'banner-dlya-polnoj-versii' );
                    $mob_banner = $helper->wp_get_post_by_slug( 'banner-dlya-mob-versii' );
                    ?>

                    <img src="<?=get_the_post_thumbnail_url( $full_banner->ID, 'full')?>" class="hidden-xs">
                    <img src="<?=get_the_post_thumbnail_url( $mob_banner->ID, 'full')?>" class="visible-xs">
                </div>

                <?php
                if($product_categories):?>
                    <div class="big-banner-text">
                    <ul class="btn-list list-inline">
                        <?php
                        foreach ( $product_categories as $product_category ):?>
                        <li><a class="btn-alt" data-category="<?=$product_category->slug?>" href="<?= get_term_link($product_category) ?>"><?=$product_category->name?></a></li>
                        <?php
                        endforeach;
                        ?>
                    </ul>
                    </div>
                <?
                endif;
                ?>
            </div>
        </div>
        <!-- /big banner -->

        <!-- big banner2 -->
        <div class="container-fluid">
            <div class="row">
                <div class="big-banner">
                    <!--<img class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/promoOPT3.jpg">
                    <img class="visible-xs" src="/wp-content/themes/lion-of-porches/img/promoOMEN2.jpg">-->

                    <?php
                    $full_banner = $helper->wp_get_post_by_slug( 'banner-na-glavnuju-2-desktop' );
                    $mob_banner = $helper->wp_get_post_by_slug( 'banner-na-glavnuju-2-mobile' );
                    ?>

                    <img src="<?=get_the_post_thumbnail_url( $full_banner->ID, 'full')?>" class="hidden-xs">
                    <img src="<?=get_the_post_thumbnail_url( $mob_banner->ID, 'full')?>" class="visible-xs">
                </div>

                <?php
                /*if($product_categories):*/?><!--
                    <div class="big-banner-text">
                        <ul class="btn-list list-inline">
                            <?php
/*                            foreach ( $product_categories as $product_category ):*/?>
                                <li><a class="btn-alt" href="<?/*= get_term_link($product_category) */?>"><?/*=$product_category->name*/?></a></li>
                            <?php
/*                            endforeach;
                            */?>
                        </ul>
                    </div>
                --><?/*
                endif;*/
                ?>
            </div>
        </div>
        <!-- /big banner -->

        <!-- video -->
        <div class="container-fluid">
            <!--<div class="row video">
                <video loop="" autoplay="" preload="auto" muted="" id="video_inicial">
                    <source src="https://imagens.lionofporches.pt/videos/ditch2.mp4" type="video/mp4">
                </video>
            </div>-->
        </div>
        <!-- /video -->

        <!-- delimiter -->
        <div class="page-sep col-xs-12">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAVCAMAAABxCz6aAAAAeFBMVEVMaXHeChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChpoOs6ZAAAAJ3RSTlMA8OCgMMBQEIBgcJALA0ywDHyXCIhAtQEuPeICavcYEnmHHg7ECqhJSmy5AAAAoklEQVR42m3Q2RKDIAwFUEpZq1ar3fc9//+HvYbMUGvvQyYcogwo5YkoVcQrTsbFPGNZE8X7Q2fkYNRx7fFijOEfXL8wovLojG7OYWFtA9RWMAUtMKj/OEFOv4i+LYqi7TEgtaCXK0iA2OyAXWBYYrlSnNcUGdw9fzvGGOORaF0maax90tvJns9jOHGM5izdrkpYyVtpvefBQQjZHsboN6n9AL9IGjFswE2+AAAAAElFTkSuQmCC" alt="Lion of Porches">
        </div>
        <!-- /delimiter -->

        <!-- product category -->
        <div class="container product-category-list">
            <div class="row">
        <?php
        if ( $product_categories ) {
            $i = 0;

            foreach ( $product_categories as $product_category ) {
                //$helper->dump($product_category);
                $image = $helper->getCategoryImage($product_category, [500, 750]);
                ?>
                <div class="col-md-6 col-xs-6">
                    <a href="<?= get_term_link($product_category) ?>"><?= $image ?></a>
                    <p class="category-name">
                        <a href="<?= get_term_link($product_category) ?>"><?= $product_category->name ?></a>
                    </p>
                </div>
                <?php
                $i++;

                if($i > 1) {
                    $i = 0;
                ?>
                </div>
                <div class="row">
                <?php
                }
            }
        }
        ?>
        </div>
        <!-- /product category -->

        <!-- lifeStyle -->


        <div class="container lifestyle">

            <h2>#lionofporches</h2>

            <div class="row">
                <div class="page-sep col-xs-12">
                    <p>Lion of Porches lifestyle</p>
                </div>
            </div>

            <?php echo do_shortcode('[instagram-feed]'); ?>

        </div>

        <!-- /lifeStyle -->

    </div>
</section>
<?php get_footer(); // подключаем footer.php ?>