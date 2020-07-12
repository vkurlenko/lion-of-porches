<?php
/**
 * Template Name: Контактная форма (contact.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>


<section class="">
    <div class="container-fluid">
        <div class="hidden-xs">
            <?=get_the_post_thumbnail( $post->id, 'full', array('class' => 'alignleft') )?>
        </div>
        <div class="visible-xs">
            <img src="/wp-content/themes/lion-of-porches/img/Screenshot_1.jpg">
        </div>
    </div>

    <div class="container">
        <!--<div class="row  stores-qd-v1-wrapper">-->


            <div class="row">
                <div class="col-md-6 ">
                    <h2><?=$post->post_title;?></h2>
                    <!-- wp:paragraph -->
                    <p> Оставайтесь с нами на связи</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph -->
                    <p>Остались вопросы? Служба  поддержки всегда готова ответить на все ваши вопросы и предоставить  информацию о продуктах и ​​/ или услугах Lion of Porches.  Мы также  открыты для получения ваших предложений для улучшения качества нашей  работы.</p>
                    <!-- /wp:paragraph -->
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 ">
                    <img  class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/Screenshot_1.jpg">
                </div>

                <div class="col-md-6  carreiras-form-wrapper">
                    <p><?=$post->post_content;?></p>
                </div>
            </div>
        <!--</div>-->
    </div>


</section>


<?php get_footer(); // необходимо для работы плагинов и функционала  ?>
