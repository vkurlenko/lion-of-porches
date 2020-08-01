<?php
/**
 * Template Name: Партнерам (forpartners.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>


<section class="carreiras">
    <div class="container-fluid">
        <div class="hidden-xs">
            <?=get_the_post_thumbnail( $post->id, 'full', array('class' => 'alignleft') )?>
        </div>
        <div class="visible-xs">
            <img src="/wp-content/themes/lion-of-porches/img/Screenshot_1.jpg">
        </div>
    </div>

    <div class="container">
        <div class="row  stores-qd-v1-wrapper">
            <div class="col-md-6 col-md-offset-3">
                <h2><?=$post->post_title;?></h2>

                <div class="carreiras-form-wrapper">

                    <!--<h2>Контактная форма</h2>-->

                    <?=do_shortcode('[contact-form-7 id="17259" title="Контактная форма"]');?>
                </div>
            </div>
        </div>
    </div>


</section>


<?php get_footer(); // необходимо для работы плагинов и функционала  ?>
