<?php
/**
 * Template Name: Шаблон Концепт (concept.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>


<section class="stores-qd-v1-wrapper">
    <!-- slick-slider-stores -->
    <div class="container-fluid slider-stores concept">
        <?php
        //$posts = query_posts('category=stores');
        $posts = query_posts('category__in=1480');

        if($posts):?>
            <div class="slider1">
                <?php
                $size = [1860, 963];
                foreach($posts as $post):?>
                    <div><?=get_the_post_thumbnail( $post->id, $size, array('class' => 'alignleft') )?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="stores-qd-v1-locator">
            <div class="stores-qd-v1-border"></div>
            <h3>
                <span>The Original</span>
                <span>British Style</span>
            </h3>
            <p class="stores-qd-v1-subtitle">концепт</p>
            <?=$post->post_content;?>
        </div>

    </div>
    <!-- /slick-slider-stores -->

</section>


<?php get_footer(); // подключаем footer.php ?>
