<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 content">

                <?php
                    if(!is_cart()) {
                        get_template_part( 'parts/user-level-block');
                    }
                ?>

                <?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
                        <h1><?php the_title(); // заголовок поста ?></h1>
                        <?php the_content(); // контент ?>
                    </article>
                <?php endwhile; // конец цикла ?>
            </div>
        </div>
    </div>
</section>


<?php get_footer(); // необходимо для работы плагинов и функционала  ?>
