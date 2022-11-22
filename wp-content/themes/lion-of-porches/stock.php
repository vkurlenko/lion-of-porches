<?php
/**
 * Template Name: Шаблон Акции (stock.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
$helper = new Helper();
$woo = new WooHelper();
$crm = new Crm();

get_header();

?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 content">

                <h2><?=$post->post_title;?></h2>

                <?php
                $url = parse_url ( $_SERVER['REQUEST_URI']);

                $category = null;//'woman';

                if(isset($url['query'])) {
                    $q = explode('=', $url['query']);

                    if(!empty($q)) {
                        $category = $q[1];
                    }
                }

                if($category) {
                    echo do_shortcode('[products tag="stock" category="'.$category.'" cat_operator="AND" orderby="date" order="DESC"]');
//                    echo do_shortcode('[products tag="stock" category="man, aksessuary-mujchiny" cat_operator="AND" orderby="date" order="DESC"]');

                } else {
                   ?>
                    <?php
                    $product_categories = $helper->getTopCategory();
                    if($product_categories) {?>
                        <?php
                        if($woo->isStockTagProducts()) {
                            ?>
                            <ul class="stock-categories">
                            <?php
                            foreach ( $product_categories as $product_category ) {
                                if (WooHelper::isSkippedCategory($product_category)) {
                                    continue;
                                }
                                ?>
                                <li>
                                    <a
                                       data-subcategory="<?=$product_category->slug?>"
                                       href="?cat=<?=$product_category->slug?>"
                                    ><?=$product_category->name?></a>

                                    <div class="container-fluid ">
                                        <div class="container">
                                            <?php
//                                               var_dump(get_categories( $product_category->term_id ));
//                                                $helper->getSabCategoryTree($product_category->term_id, 1);
                                            ?>
                                            <?php
                                            if ($product_category->slug == 'woman') {
                                                ?>
                                                <ul>
                                                    <li class="cat-item show-all"><a href="?cat=woman">Показать все</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,verhnyaya-odejda">Верхняя одежда</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,trikotaj">Трикотаж</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,platyayubki">Платья/Юбки</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,rubashki">Рубашки/Блузки</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,bryukishorty">Брюки/шорты</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,obuv">Обувь</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=woman,aksessuary">Аксессуары</a>
                                                    </li>
                                                </ul>
                                                <?php
                                            } elseif ($product_category->slug == 'man') {
                                                ?>
                                                <ul>
                                                    <li class="cat-item show-all"><a href="?cat=man">Показать все</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,verhnyaya-odejda-mujchiny">Верхняя одежда</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,trikotaj-mujchiny">Трикотаж</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,rubashki-mujchiny">Рубашки</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,bryukishorty-mujchiny">Брюки/шорты</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,aksessuary-mujchiny">Аксессуары</a>
                                                    </li>
                                                    <li class="cat-item"><a href="?cat=man,obuv-man">Обувь</a>
                                                    </li>
                                                </ul>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            </ul>
                        <?php
                        }
                    }
                }
                ?>

            </div>
        </div>
    </div>
</section>

<?php get_footer();



