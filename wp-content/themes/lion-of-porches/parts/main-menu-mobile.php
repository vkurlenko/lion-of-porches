<div class="row visible-xs">
    <div class="main-menu mobile hidden">

        <?php
        $helper = new Helper();
        $product_categories = $helper->getTopCategory();

        if($product_categories):?>
            <ul>
                <?php
                foreach ( $product_categories as $product_category ):?>
                    <li><a class="btn-alt" href="<?= get_term_link($product_category) ?>"><?=$product_category->name?></a></li>
                <?php
                endforeach;
                ?>
            </ul>
        <?
        endif;
        ?>

        <!--<form>
            <div class="row open-search mobile">
                <div class="col-xs-6"><input type="text" value="" placeholder="поиск"></div>
                <div class="col-xs-6  btn-search"><input type="submit" value="Найти" class="btn btn-primary btn-submit"></div>-->
                <!--<span class="glyphicon glyphicon-search" aria-hidden="true"></span>-->
            <!--</div>
        </form>-->
        <?php if ( function_exists( 'aws_get_search_form' ) ) { aws_get_search_form(); } ?>
        <?php /*echo do_shortcode('[wcas-search-form]'); */?>

        <div class="row">
            <div class="col-xs-6">
                <?
                $args = array(
                    'menu' => 'top-menu-2', // какое меню нужно вставить (по порядку: id, ярлык, имя)
                );
                wp_nav_menu($args);?>
            </div>
            <div class="col-xs-6">
                <ul>
                    <li><a href="/shop/">Каталоги</a></li><li><a href="/forpartners/">Партнерам</a></li><li><?php get_template_part( 'parts/login-link');?></li>
                </ul>
            </div>
        </div>

    </div>
</div>