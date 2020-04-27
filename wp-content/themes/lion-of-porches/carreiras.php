<?php
/**
 * Template Name: Шаблон Вакансии (carreiras.php)
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
            <img src="/temp/Screenshot_1.jpg">
        </div>
    </div>

    <div class="container">
        <div class="row  stores-qd-v1-wrapper">
            <div class="col-md-6 content">
                <h2><?=$post->post_title;?></h2>

                <p><?=$post->post_content;?></p>

                <img  class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/Screenshot_1.jpg">

            </div>

            <div class="col-md-6 carreiras-form-wrapper">

                <h2>Заявка на вакансию</h2>

                <?php echo do_shortcode('[contact-form-7 id="80" title="Contact form carreiras"]');?>
            </div>
        </div>
    </div>


</section>


<?php get_footer(); // подключаем footer.php ?>
