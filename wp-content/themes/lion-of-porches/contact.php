<?php
/**
 * Template Name: Контактная форма (contact.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 content">

                <?php
                get_template_part( 'parts/user-level-block');
                ?>

                <div class="container">
                    <div class="row  stores-qd-v1-wrapper">
                        <div class="col-md-6 content">
                            <h2><?=$post->post_title;?></h2>

                            <p><?=$post->post_content;?></p>

                            <!--<img  class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/Screenshot_1.jpg">-->

                        </div>

                        <div class="col-md-5 col-md-offset-1 carreiras-form-wrapper">

                            <h2>Контактная форма</h2>

                            <?=do_shortcode('[contact-form-7 id="17259" title="Контактная форма"]');?>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>


<?php get_footer(); // необходимо для работы плагинов и функционала  ?>
