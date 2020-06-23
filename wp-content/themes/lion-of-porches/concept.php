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
            <p>Aware of the constant changes in the modern era, Lion of Porches arises, renewed based on a sense of liberty and innovation.
                The brand gives privilege to the connection between traditional and contemporary. Settled on this premise, Lion of Porches reflects a new coolness through a modern design and a product development which follow tradition and British values, transforming simplicity into sophistication.
            </p>
            <p>The goal is clear: take over the world, being responsible for your own choices and for the conquest of a lifestyle that promotes a better quality of life, self-esteem, union and happiness.</p>
        </div>

    </div>
    <!-- /slick-slider-stores -->

</section>


<?php get_footer(); // подключаем footer.php ?>
