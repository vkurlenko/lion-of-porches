<?php
/**
 * Template Name: Шаблон размерная сетка (sizeguide.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header();

$h = new Helper();
?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 content sizeguide">

                <h2><?=$post->post_title;?></h2>
               <!-- --><?/*=$post->post_content;*/?>

                <?php
                $cat = get_category_by_slug('sizeguide');
                $id = $cat->term_id;

                $args = array(
                    'child_of'     => $id,
                    'orderby'      => 'name',
                    'order'        => 'ASC',
                    'hide_empty'   => 0,
                    'hierarchical' => 1,
                    'number'       => 0, // сколько выводить?
                );

                $categories = get_categories( $args );

                //(new Helper())->dump($categories);
                ?>

                <ul class="nav nav-tabs category-tabs" role="tablist">
                    <?php
                    $i = 0;
                    foreach($categories as $category) {
                        ?><li class="<?=!$i ? 'active' : ''?>"><a data-toggle="tab" href="#<?=$category->slug?>"><?=$category->name?></a></li><?php
                        $i++;
                    }
                    ?>
                </ul>

                <div class="tab-content">

                    <?php
                    $i = 0;
                    foreach($categories as $category) {
                        ?>
                        <div id="<?=$category->slug?>" class="tab-pane fade <?=!$i ? 'in active' : ''?>">
                        <?php
                            echo $h->getSizeGuideTab($category->slug);
                        ?>
                        </div>
                        <?php
                        $i++;
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</section>

<?php get_footer();
