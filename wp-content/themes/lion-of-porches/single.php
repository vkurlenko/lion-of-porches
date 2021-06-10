<?php
/**
 * Template Name: Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>


<section>
	<div class="container">
		<div class="row">
            <div class="col-md-12  content">
                <div class="row">
                    <div class="col-md-12 woocommerce-breadcrumb" >
                        <?php if( function_exists('kama_breadcrumbs') ) kama_breadcrumbs('<span class="breadcrumb-delimiter">|</span>'); ?>
                    </div>
                </div>
                <?php get_template_part( 'parts/user-level-block');?>
                <?php
               /* $post = get_post();
                var_dump($post);*/
                ?>

                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <h1><?=$post->post_title;?></h1>
                        <?=$post->post_content;?>
                    </div>
                </div>

            </div>
		</div>
	</div>
</section>
<?php get_footer(); // подключаем footer.php ?>
