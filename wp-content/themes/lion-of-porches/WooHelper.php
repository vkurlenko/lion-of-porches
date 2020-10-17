<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.05.2020
 * Time: 21:45
 */

//use Helper;

class WooHelper
{
    const CART_CONTENTS_COUNT_MAX = 5;
    /**
     *  Загрузка каталога товаров из текстового файла
     */
    public function createVarProductsFromFile()
    {
       /* $this->create_attribute( $raw_name = 'pa_color', $terms = array( 'хаки' ) );
        return;*/

        //$f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog_full.txt');
        //$f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog_mini.txt');
        $f = file($_SERVER['DOCUMENT_ROOT'].'/temp/Nomenklatura3.csv');
        echo $_SERVER['DOCUMENT_ROOT'].'/temp/Nomenclatura3.csv';
        //$f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog_full_2.txt');

        // создадим массив артикулов
        $arr = [];

        foreach($f as $str) {

            // пропустим пустые строки
            if(trim($str) == '') {
                continue;
            }

            echo $str.'<br>';
            //continue;

            // разберем строку на элементы массива
            //Женщины|Верхняя одежда|Блейзеры||5604205986388|L101052038|Blazer|Blazer|Блейзер|L101052038 M 580 SS20|M|синий|62% полиэстер 37% иск. шелк 1% эластан|Португалия|L101052038_580_1 (2)|23 290|Джинсовый .||
            $item = explode(';', $str);
            $item_data = explode(',', $item[9]); // L101052038	 M	 580	 SS20

            // с 0 по 3 элемент - дерево названий категорий
            $item_tree = array_slice($item, 0, 4);

            // создадим дерево категорий и получим массив ID категорий товара
            $parent_cats = $this->createCategoryTree($item_tree, 0);
            //$this->dump($parent_cats);
            //die;

            // добавим позицию в массив с привязкой к артикулу
            $arr[$item[5]][] = [
                'cat'           => $parent_cats,//$item_tree,
                //'sku'           => $item[5].'.'.$item_data[2],
                'sku'           => $item[5],
                'post_title'    => $item[8],
                'post_excerpt'  => $item[8],
                'size'          => $item[10], // размер
                'color'         => $item[11], // цвет (рус.)
                'material'      => $item[12], // материал
                'vendor'        => $item[13], // страна
                'price'         => $item[15], // цена
                'post_content'  => $item[16], // описание
                'stock'         => $item[17], // остатки
                'data'          => $item_data
            ];

            if(!isset($arr[$item[5]]['size'])) {
                $arr[$item[5]]['size'] = [];
            }

            if(!in_array($item[10], $arr[$item[5]]['size'])) {
                $arr[$item[5]]['size'][] = $item[10];
            }

            if(!isset($arr[$item[5]]['color'])) {
                $arr[$item[5]]['color'] = [];
            }

            if(!in_array($item[11], $arr[$item[5]]['color'])) {
                $arr[$item[5]]['color'][] = $item[11];
            }

            //$this->dump($arr); //die;
        }

        //$this->dump($arr); //die;

        // создадим вариативные товары
        foreach($arr as $sku) {
            $this->createVarProduct($sku); //die;
        }

        //$this->dump($arr); die;
    }

    //public function createVarProduct($cats = [25], $data = [])
    public function createVarProduct($sku = [])
    {
        global $wpdb;

        //$this->dump($sku); //return; //die;

        $data = $sku[0];
        $cats = $data['cat'];
        $size = $sku['size'];
        $color = $sku['color'];
        $color_code = $data['data'][2];
        $description = sprintf ('Страна производства: %s<br>Материал: %s<br>%s', $data['vendor'], $data['material'], $data['post_content']);
        //echo $color_code.'<br>';

        $attr = $this->getAttributes();

        $post = array(
            'post_title'   => $data['post_title'],//"Product with Variations2",
            'post_content' => $description, //$data['post_content'],//"product post content goes here…",
            'post_status'  => "publish",
            'post_excerpt' => $data['post_excerpt'],//"product excerpt content…",
            //'post_name'    => $data['post_name'],//"test_prod_vars2", //name/slug
            'post_type'    => "product"
        );

        echo $data['sku'];

        //var_dump(wc_get_product_id_by_sku('L101052038'));// попробуем найти товар по его артикулу


        if(!wc_get_product_id_by_sku($data['sku'])) {
            //Create product/post:
            $new_post_id = wp_insert_post( $post, false );
        } else {
            $new_post_id = wc_get_product_id_by_sku($data['sku']);
        }

        //make product type be variable:
        wp_set_object_terms ($new_post_id,'variable','product_type');

        //add category to product:
        wp_set_object_terms( $new_post_id, $cats, 'product_cat');

        // размеры
        $avail_attributes = $size;
        wp_set_object_terms($new_post_id, $avail_attributes, 'pa_size');

        // цвета
        $avail_attributes2 = $color;
        wp_set_object_terms($new_post_id, $avail_attributes2, 'pa_color');

        $thedata = Array(
            'pa_size'=>Array(
                'name'=>'pa_size',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'),

            'pa_color'=>Array(
                'name'=>'pa_color',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'),
        );

        update_post_meta( $new_post_id,'_product_attributes', $thedata);
        //update_post_meta( $new_post_id, '_sku', $data['sku'].'.'.$data['data'][2]);
        update_post_meta( $new_post_id, '_sku', $data['sku']);
        update_post_meta( $new_post_id, '_visibility', 'visible' );

        $i = 1;
        $variation_id = $new_post_id + 1;

        foreach($sku as $variation) {

            if(key($variation) == 'size' || key($variation) == 'color') {
                continue;
            }

            $variation_size = $attr['size'][mb_strtolower($variation['size'])];

            /*if(!isset($attr['color'][mb_strtolower($variation['color'])])) {
                $new_attr_color = mb_strtolower($variation['color']);

                echo 'нет атрибута '.$new_attr_color.'<br>';

                //$this->create_attribute( $raw_name = 'pa_color', $terms = array( $new_attr_color ) );

                $args = [
                    'name' => $new_attr_color,
                    'slug' => (new Helper())->translit($new_attr_color)
                ];

                wc_create_attribute( $args );

                $attr = $this->getAttributes();
            }*/

            $variation_color = $attr['color'][mb_strtolower($variation['color'])];
            $variation_sku = $variation['sku'].'.'.$variation['data'][2];
            $variation_descr = sprintf ('Страна производства: %s<br>Материал: %s<br>%s', $variation['vendor'], $variation['material'], $variation['post_content']);
            $variation_stock = $variation['stock'];

            echo sprintf('post_id: %s, SKU %s: color %s, size %s, price %s <br> %s', $new_post_id, $variation_sku, $variation_color, $variation_size, $variation['price'], $variation_descr);/* вариация 1 */

            $my_post = array(
                'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
                'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
                'post_status' => 'publish',
                'post_parent' => $new_post_id,//post is a child post of product post
                'post_type' => 'product_variation',//set post type to product_variation
                'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
            );

            if(!wc_get_product_id_by_sku($variation_sku)) {
                //Create product/post:
                $attID = wp_insert_post( $my_post );
            } else {
                $variation_id = wc_get_product_id_by_sku($variation_sku);
            }

            update_post_meta($variation_id, 'attribute_pa_size', $variation_size);
            update_post_meta($variation_id, 'attribute_pa_color',  $variation_color);
            update_post_meta($variation_id, '_price',  $variation['price']);
            update_post_meta($variation_id, '_regular_price', $variation['price']);
            update_post_meta($variation_id, '_variation_description', $variation_descr);
            update_post_meta($variation_id, '_sku', $variation_sku);
            update_post_meta($variation_id, '_stock_status', 'instock');
            update_post_meta($variation_id, '_stock', $variation_stock);
            update_post_meta($variation_id, '_manage_stock', 'yes');

            wp_set_object_terms($variation_id, $avail_attributes, 'pa_size');

            $thedata = Array(
                'pa_color'=>Array(
                    'name'=>$variation_color,
                    'value'=>'',
                    'is_visible' => '1',
                    'is_variation' => '1',
                    'is_taxonomy' => '1'
                ),
                'pa_size'=>Array(
                    'name'=> $variation_size,
                    'value'=>'',
                    'is_visible' => '1',
                    'is_variation' => '1',
                    'is_taxonomy' => '1'
                )
            );

            update_post_meta( $variation_id,'_product_attributes',$thedata);

            $variation_id++;
        }
    }

    /**
     * Создание дерева категорий товара
     *
     * @param array $tree
     * @param int $ParentCatID
     * @return array|bool|false|int|object|WP_Term
     */
    public function createCategoryTree($tree = [], $ParentCatID = 0)
    {
        $arr_tree = [];
        foreach($tree as $level) {
            //echo $level.'<br>';
            //$this->dump($ParentCatID);
            //echo 'Проверим существование категории '.$level.' parent='.$ParentCatID.'<br>';
            $cat_id = $this->SearchCat ($level, $ParentCatID);
            //echo $cat_id.'<br>';
            //$this->dump($cat_id);

            if($cat_id == false) {
                //echo 'нет категории '.$level.'<br>';
                //$this->dump($cat_id);
                echo ' создание категории ' . $level.' parent_id = '. $ParentCatID.'<br>';
                $cat_id = $this->createCategory($level, $ParentCatID);
            } else {
                //echo 'есть категория '.$level.'<br>';
                $ParentCatID = $cat_id;
            }

            //echo '<br>';

            $ParentCatID = $cat_id;
            $arr_tree[] = $ParentCatID;
            //echo $ParentCatID.'<br>';
        }

        return $arr_tree;//$cat_id;
    }

    /**
     * Создание категории товара
     *
     * @param $CatName
     * @param $ParentCatID
     * @return int|object
     */
    public function createCategory($CatName, $ParentCatID)
    {
        //echo 'create category '.$CatName.' ' .$ParentCatID.'<br>';
        $cat_defaults = array(
            //'cat_ID' => $CatID,                // ID категории, которую нужно обновить. 0 - добавит новую категорию.
            'cat_name' => $CatName,             // название категории. Обязательный.
            'category_description' => '', // описание категории
            'category_nicename' => (new Helper())->translit($CatName),      // слаг категории
            'category_parent' => $ParentCatID,        // ID родительской категории
            'taxonomy' => 'product_cat'      // таксономия. Измените, чтобы добавить элемент другой таксономии. Например для меток будет post_tag
        );

        require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
        //print_r ($cat_defaults );
        //О функции добавления категорий, устнарение ошибки Call to undefined function wp_insert_category()
        //http://wp-kama.ru/function/wp_insert_category
        $cat_id = wp_insert_category( $cat_defaults, true);

        return $cat_id;
    }

    /**
     * Функция ищет категорию по названию.
     * Возвращает Wordpress id категории, если найдена и false, если нет
     *
     * @param $Cat
     * @return array|bool|false|WP_Term
     */
    public function SearchCat ($Cat, $ParentCatID)
    {
        if($Cat == '') {
            return $ParentCatID;
        }

        $cat_id = get_term_by( 'name', $Cat, 'product_cat', 'OBJECT', 'raw' );

        $product_categories = get_categories( array(
            'taxonomy'     => 'product_cat',
            'orderby'      => 'name',
            'pad_counts'   => false,
            'hierarchical' => 0,
            'hide_empty'   => false,
            'name' => $Cat,
            'parent' => $ParentCatID
        ) );

        if (isset($product_categories[0]->term_id)) {
            if($product_categories[0]->parent == $ParentCatID) {
                return $product_categories[0]->term_id;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Возвращает массив атрибутов товаров
     *
     * @return array
     */
    public function getAttributes()
    {
        $arr = [];

        $attr = wc_get_attribute_taxonomies();

        foreach($attr as $a) {
            $arr[$a->attribute_name] = [];

            $terms = get_terms("pa_".$a->attribute_name, [
                'hide_empty' => false,
            ]);

            foreach($terms as $term) {
                $arr[$a->attribute_name][mb_strtolower($term->name)] = $term->slug;
            }
        }

        //$this->dump($arr); die;

        return $arr;
    }

    /**
     * Получим все доступные для заказа размеры товара
     *
     * @param $product
     * @return array
     */
    public function getProductVariationsAttributes($product)
    {
        $arr = [];

        if($product) {
            $variations = $product->get_available_variations();

            foreach($variations as $v) {
                $attr_color = $v['attributes']['attribute_pa_color'];
                $attr_size = $v['attributes']['attribute_pa_size'];
                $is_in_stock = $v['is_in_stock'];

                /*if(!isset($arr[$attr_color])) {
                    $arr[$attr_color] = [];
                }

                $arr[$attr_color][] = [
                        'size' => $attr_size,
                        'is_in_stock' => $is_in_stock
                ];*/

                if($is_in_stock) {
                    if(!in_array($attr_size, $arr)) {
                        $arr[] = strtolower($attr_size);
                    }
                }
            }
        }

        sort($arr);

        $arr = $this->sortSize($arr);

        return $arr;
    }

    /**
     * Получим все доступные для заказа размеры товара в этом цвете
     *
     * @param $product
     * @param null $color
     * @return array
     */
    public function getVariationSizesByColor($product, $color = null)
    {
        $arr = [];

        if($product && $color) {
            $variations = $product->get_available_variations();

            foreach($variations as $v) {

                if($color == $v['attributes']['attribute_pa_color']) {
                    $attr_color = $v['attributes']['attribute_pa_color'];
                    $attr_size = $v['attributes']['attribute_pa_size'];
                    $is_in_stock = $v['is_in_stock'];

                    if($is_in_stock) {
                        if(!in_array($attr_size, $arr)) {
                            $arr[] = strtolower($attr_size);
                        }
                    }
                }
            }
        }

        sort($arr);

        $arr = $this->sortSize($arr);

        return $arr;
    }

    /**
     * Сортировка размеров
     *
     * @param $arr
     * @return array
     */
    public function sortSize($arr)
    {
        $sort = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        $newArr = [];

        foreach($sort as $item) {

            if(in_array(strtolower($item), $arr)) {
               //echo $item;
                $newArr[] = $item;
            }
        }

        return !(empty($newArr)) ? $newArr : $arr;
    }

    /**
     * Вывод в каталоге вариаций товара как отдельных товаров
     *
     * @param $product
     */
    public function getVariationsAsProduct($product, $post)
    {
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        foreach ($terms as $term) {
            $product_cat[] = 'product_cat-'.$term->slug;
            //break;
        }

        /*echo implode(' ', $product_cat);

        (new Helper())->dump($terms);
        (new Helper())->dump($product->get_type());
        die;*/
        /*
         * todo
         * 1. +классы в <li>
         * 2. цена распродажи
         * 3. +размеры картинки
         * 4. +кол-во в остатке
         * 5. flash теги
         * */

        // получим все вариации товара
        $variations = $product->get_available_variations();

        // размер картинки берем из настроек сайта
        $image_size = [get_option('woocommerce_thumbnail_image_width'), get_option('woocommerce_thumbnail_image_width')];

        // ссылка на вариацию в карточке товара
        $url = '/product/'.$product->get_slug().'/?';

        // классы тега <li>
        $class_cats = implode(' ', $product_cat);

        $arr_pa_color = [];

        foreach ($variations as $key => $value) {
            /*(new Helper())->dump($value); //die;
            continue;*/

            /* пропускаем повторяющиеся цвета товара */
            if(!in_array($value['attributes']['attribute_pa_color'], $arr_pa_color)) {
                $arr_pa_color[] = (new Helper())->translit($value['attributes']['attribute_pa_color']);
            } else {
                continue;
            }

            // к ссылке добавим get-параметры для перехода именно на вариацию
            $v_url = $url.'attribute_pa_size='.$value['attributes']['attribute_pa_size'].'&attribute_pa_color='.$value['attributes']['attribute_pa_color'];
            ?>

            <li class="product type-product post-<?=$product->get_id()?> status-<?=$product->get_status()?> <?=$value['is_in_stock'] ? 'instock' : ''?> <?=$class_cats?> shipping-<?=$product->get_tax_status()?> product-type-<?=$product->get_type()?>">

                <a href="<?=$v_url?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">

                    <div class="flash-tags">
                        <?php if ( $product->is_on_sale() && ($value['display_price'] != $value['display_regular_price'])) : ?>

                            <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product ); ?>

                        <?php
                        endif;

                        /* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
                        $posttags = get_the_terms( $product->get_id(), 'product_tag' );

                        if($posttags) {
                            foreach($posttags as $tag) {
                                if(!in_array($tag->slug, (new WooHelper())->getVisibleTags())) {
                                    continue;
                                }
                                ?>
                                <?php /*echo apply_filters( 'woocommerce_sale_flash', '<a href="/product-tag/'.$tag->slug.'/"><span class="prod-tag '.$tag->slug.'">'.$tag->name.'</span></a>', $post, $product ); */?>
                                <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="prod-tag '.$tag->slug.'">'.$tag->name.'</span>', $post, $product ); ?>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <?php
                    if(!get_the_post_thumbnail( $value['variation_id'])) {
                        ?><img width="1" height="1" src="<?=wc_placeholder_img_src( $image_size )?>" class="woocommerce-placeholder wp-post-image" alt="Заполнитель"><?php
                    } else {
                        echo get_the_post_thumbnail( $value['variation_id'], $image_size);
                    }
                    ?>

                    <span class="art"><?=$product->get_sku()?></span>

                    <div class="colors-bar">
                        <span><?=$this->getColorTitles()[$value['attributes']['attribute_pa_color']]?></span>
                        <!--<span class="color <?/*=$value['attributes']['attribute_pa_color']*/?>" title="<?/*=$this->getColorTitles()[$value['attributes']['attribute_pa_color']]*/?>"></span>-->
                    </div>

                    <h1 class="woocommerce-loop-product__title"><?=$product->get_name()?></h1>

                        <?php
                       // $p = $this->getPriceFromHtml($product);

                            ?>
                            <span class="price"><?=wc_price( $value['display_price'] )?>

                                <?php
                                /************************************************/
                                /* указание на карточке общей скидки */
                                /************************************************/
                                $discount = '';
                                if($value['display_price'] != $value['display_regular_price']) {
                                    $discount = $this->getSalePercent($value['display_price'], $value['display_regular_price']);
                                    $discount = sprintf('-%s%%', $discount);

                                    ?><del class="inline"><?=wc_price($value['display_regular_price'])?></del><?php
                                }
                                /************************************************/
                                ?>

                            </span>
                            <?php
                            /************************************************/
                            /*  указание на карточке персональной скидки */
                            /************************************************/

                            /*
                            $personal_price = $this->getPersonalPrice($value['display_price']);

                            if($personal_price && ($personal_price != $value['display_price'])):*/?><!--
                            <span class="price"><?/*=wc_price( $personal_price )*/?>
                                <del class="inline"><?/*=wc_price($value['display_price'])*/?></del>
                            </span>
                            <?php
                            /* else:*/?>
                                <span class="price"><?/*=wc_price( $value['display_price'] )*/?></span>
                            --><?php /*endif;
                            */?>

                            <?php
                            /*if((new Crm())->getCurrentUserDiscount()) {
                                $discount = sprintf('-%s%%', (new Crm())->getCurrentUserDiscount());
                            } else {
                                $discount = '';
                            }
                            */
                            /************************************************/
                            ?>

                            <span class="personal-discount"><?=$discount?></span>

                </a>

                <a href="<?=$v_url?>" data-quantity="<?=$value['max_qty']?>" class="button box-item btn-quickview product_type_variable" data-product_id="<?=$product->get_id()?>" data-product_sku="<?=$product->get_sku()?>" aria-label="Выбрать опции для &quot;<?=$product->get_name()?>&quot;" rel="nofollow">Подробнее</a>

                <!-- доступные для заказа размеры -->
                <div class="sizes-bar">
                    <?php
                    $sizes = $this->getVariationSizesByColor($product, $value['attributes']['attribute_pa_color']);

                    foreach($sizes as $size) {
                        echo '<span>'.$size.'</span>';
                    }

                    ?>
                </div>
                <!-- /доступные для заказа размеры -->
            </li>

            <?php
        }
    }

    /**
     * Получить названия цветов по slug
     *
     * @return array
     */
    public function getColorTitles()
    {
        $arr = [];

        $colors = $this->getAttributes()['color'];

        foreach($colors as $title => $slug) {
            $arr[$slug] = $title;
        }

        return $arr;
    }


    /**
     * Крошки на странице продукта
     *
     * @param $post
     * @return array
     */
    public function getProductBreadcrumb($post)
    {
        $breadcrumb = [];

        $terms = get_the_terms( $post->ID, 'product_cat' );
        foreach ($terms as $term) {
            $name = $term->name;
            $link = get_term_link( $term->term_id, 'product_cat' );

            $breadcrumb[] = [$name, $link];
        }

        return $breadcrumb;
    }

    /**
     * Пересчет стоиомости корзины с учетом персональной скидки
     *
     * @param $cart
     * @return bool
     */
    public function getCartSubTotal($cart)
    {
        $h = new Helper();
        $crm = new Crm();

        $discount_sum = 0;

        $current_user = wp_get_current_user();

        // персональный размер скидки на все товары по обычной цене (%)
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        // коэффициент для расчета цены со скидкой
        //$p = $discount / 100;

        foreach($cart->cart_contents as $hash => $item) {

            $item_data = $this->getCartItemData($item);

            /*// получим товар
            $product = wc_get_product( $item['product_id'] );

            // получим все вариации товара
            $variations = $this->getVariation($product);

            //$h->dump($variations); //die;

            // цена вариации текущая
            $variation_price = $variations[$item['variation_id']]['display_price'];

            // цена вариации обычная
            $variation_regular_price = $variations[$item['variation_id']]['display_regular_price'];

            // является ли вариация распродажей
            $is_sale = $variations[$item['variation_id']]['display_price'] != $variations[$item['variation_id']]['display_regular_price'] ? true : false;

            // процент скидки по распродаже
            $sale_percent = $this->getSalePercent($variation_price, $variation_regular_price);

            // процент скидки по распродаже персональный
            $user_sale_percent = $crm->getUserSaleDiscount($sale_percent, $discount);

            $user_sale_percent_final = $sale_percent + $user_sale_percent;

            // полная стоимость вариации по текущей цене (с учетом количества)
            $variation_price_full = (int)$item['quantity'] * $variations[$item['variation_id']]['display_price'];

            // полная стоимость вариации по персональной цене (с учетом количества)
            if($is_sale) {
                $variation_price_personal = $variation_price_full - ($variation_price_full * $user_sale_percent_final / 100);
                $discount_sum += $variation_price_full * $user_sale_percent_final / 100;
            } else {
                $variation_price_personal = $variation_price_full - ($variation_price_full * $discount / 100);
                $discount_sum += $variation_price_full * $discount / 100;
            }*/

            /*$h->dump([
                'product_id'        => $item['product_id'],
                'product_type'      => $product->get_type(),
                'is_on_sale'        => $product->is_on_sale(),
                'variation_id'      => $item['variation_id'],
                'quantity'          => $item['quantity'],
                'line_subtotal'     => $item['line_subtotal'],
                'variation_price'   => $variation_price,
                'variation_regular_price' => $variation_regular_price,
                'is_sale'           => $is_sale,
                'sale_percent'      => $sale_percent,
                'user_sale_percent' => $user_sale_percent,
                'user_sale_percent_final' => $user_sale_percent_final,
                'price_full'        => $variation_price_full,
                'price_personal'    => $variation_price_personal
            ]);*/

            //$h->dump($item); die;
            //echo $item['line_subtotal'].'<br>';

            if($item_data['is_sale'] && $item_data['user_percent'] < 50) {
                $discount_sum += $item_data['variation_price_full'] * $item_data['user_sale_percent_final'] / 100;
            } else {
                $discount_sum += $item_data['variation_price_full'] * $discount / 100;
            }
        }

        //echo sprintf('%s - %s = %s', $cart->subtotal, $discount_sum, ($cart->subtotal - $discount_sum));

        //die;

        return $discount_sum;
    }

    /**
     * Пересчет стоимости товара в корзине с учетом персональных скидок
     *
     * @param $item
     * @param $discount
     * @return array
     */
    public function getCartItemData($item)
    {
        /*if(!is_user_logged_in()) {
            //return [];
        }*/

        $h = new Helper();
        $crm = new Crm();

        // персональный размер скидки на все товары по обычной цене (%)
        $current_user = wp_get_current_user();
        $discount = (new Crm())->getUserDiscount($current_user->user_email);

        // получим товар
        $product = wc_get_product( $item['product_id'] );

        // получим все вариации товара
        $variations = $this->getVariation($product);

        //$h->dump($variations); //die;

        // цена вариации текущая
        if($discount == 50 && $product->is_on_sale()) {
            $variation_price = $item['line_subtotal'] = $variations[$item['variation_id']]['display_regular_price'];
        } else {
            $variation_price = $variations[$item['variation_id']]['display_price'];
        }

        // цена вариации обычная
        $variation_regular_price = $variations[$item['variation_id']]['display_regular_price'];

        // является ли вариация распродажей
        $is_sale = $variations[$item['variation_id']]['display_price'] != $variations[$item['variation_id']]['display_regular_price'] ? true : false;

        // процент скидки по распродаже
        $sale_percent = $this->getSalePercent($variation_price, $variation_regular_price);

        // процент скидки по распродаже персональный
        $user_sale_percent = $crm->getUserSaleDiscount($sale_percent, $discount);

        // суммарная скидка (%распродажи + %персональный)
        //$user_sale_percent_final = is_user_logged_in() ? ($sale_percent + $user_sale_percent) : 0;
        $user_sale_percent_final = is_user_logged_in() ? ($user_sale_percent) : 0;

        // полная стоимость вариации по текущей цене (с учетом количества)
        if($discount == 50 && $product->is_on_sale()) {
            $variation_price_full = (int)$item['quantity'] * ($variation_regular_price);
        } else {
            $variation_price_full = (int)$item['quantity'] * $variations[$item['variation_id']]['display_price'];
        }

        // полная стоимость вариации по персональной цене (с учетом количества)
        if($is_sale) {
            if($discount == 50) {
                $variation_price_personal = $variation_price_full - ($variation_price_full * 50 / 100);
            } else {
                $variation_price_personal = $variation_price_full - ($variation_price_full * $user_sale_percent_final / 100);
            }
            //$discount_sum += $variation_price_full * $user_sale_percent_final / 100;
        } else {
            $variation_price_personal = $variation_price_full - ($variation_price_full * $discount / 100);
            //$discount_sum += $variation_price_full * $discount / 100;
        }

        $arr = [
            'product_id'        => $item['product_id'],
            'product_type'      => $product->get_type(),
            'is_on_sale'        => $product->is_on_sale(),
            'variation_id'      => $item['variation_id'],
            'quantity'          => $item['quantity'],
            'line_subtotal'     => $item['line_subtotal'],
            'variation_price'   => $variation_price,
            'variation_regular_price' => $variation_regular_price,
            'is_sale'           => $is_sale,
            'user_percent'      => $discount,
            'sale_percent'      => $sale_percent,
            'user_sale_percent' => $user_sale_percent,
            'user_sale_percent_final' => $user_sale_percent_final,
            'price_full'        => $variation_price_full,
            'price_personal'    => $variation_price_personal,
            'variation_price_full' => $variation_price_full,

            //'user_sale_percent_html' => $sale_percent.' + '.$user_sale_percent.' = '.$user_sale_percent_final,
            'user_sale_percent_html' => $user_sale_percent_final,
            'is_discount_price' => ($variation_price_full != $variation_price_personal)
        ];

        //$h->dump($item); die;
        //echo $item['line_subtotal'].'<br>';
        return $arr;
    }


    /**
     * Расчет процента скидки
     *
     * @param $sale_price
     * @param $regular_price
     * @return float|int
     */
    public function getSalePercent($sale_price, $regular_price)
    {
        $percent = 0;

       /*
        200 = 100%
        70 = x%$cart->subtotal
        x = 70 * 100 / 200
       */

        if($sale_price <  $regular_price) {
            $percent = floor( 100 - ($sale_price * 100 / $regular_price));
        }

        return $percent;
    }


    /**
     * Расчет цены акционного товара с учетом персональной скидки
     *
     * @param $regular_price
     * @param $sale_price
     * @return float|int
     */
    public function getPersonalSalePrice($regular_price, $sale_price)
    {
        $current_user = wp_get_current_user();

        $user_discount = (new Crm())->getUserDiscount($current_user->user_email);

        $sale_percent = $this->getSalePercent($sale_price, $regular_price);

        if($sale_percent) {

            $p = (new Crm())->getUserSaleDiscount($sale_percent, $user_discount);

            $sale_price = $sale_price - ($sale_price * $p / 100);
        }

        return $sale_price;
    }

    /**
     * Расчет цены обычного товара с учетом персональной скидки
     *
     * @param int $regular_price
     * @return float|int
     */
    public function getPersonalPrice($regular_price = 0)
    {
        $personal_price = false;//$regular_price;

        $user_discount = (new Crm())->getCurrentUserDiscount();
        //echo $user_discount;

        if($user_discount) {
            $personal_price = $regular_price - ($regular_price * $user_discount / 100);
        }

        return $personal_price;
    }

    /**
     * Получим цену товара из $product->get_price_html()
     *
     * @param $product
     * @return mixed
     */
    public function getPriceFromHtml($product)
    {
        //(new Helper())->dump($product);
        $re = '/(\d)|(\.)*/mu';

        if($product->get_price()) {
            return $product->get_price();
        }

        $str = strip_tags($product->get_price_html());//'₽7 490.50';

        //echo $str; //die;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $p = '';

        foreach($matches as $m) {
            if(is_numeric($m[0]) || $m[0] == '.') {
                $p .= $m[0];
            }
        }

        $price = str_replace('8381', '', $p);

        return $price;
    }


    /**
     * Вывод акционной цены вариации в карточке товара с учетом персональной скидки
     *
     * @param $sale_price
     * @param $regular_price
     * @return string
     */
    public function getVariationPriceTextSale($sale_price, $regular_price)
    {
        $crm = new Crm();

        $current_user = wp_get_current_user();
        $user_discount = $crm->getUserDiscount($current_user->user_email);
        $sale_discount = $this->getSalePercent($sale_price, $regular_price);

        $sale_price = '';
        $lp_sale_price = $this->getPersonalSalePrice($regular_price, $sale_price);

        if ($lp_sale_price) {
            $text = sprintf('%s', is_numeric($lp_sale_price) ? wc_price($lp_sale_price) : $lp_sale_price);
            $text .= sprintf('<div class="discount-personal"><div class="discount-value">Cкидка на товар: -%s %%</div></div>', $sale_discount);
            $text .= sprintf('<div class="discount-personal"><div class="discount-value">Ваша дополнительная скидка: -%s%% (от акционной цены)</div></div>', $crm->getUserSaleDiscount($sale_discount, $user_discount));
            $text .= sprintf('<div class="discount-personal"><div class="discount-value">Ваша экономия: %s</div></div>', wc_price($regular_price - $lp_sale_price));
            $text .= sprintf('<div class="discount-personal"><div class="discount-value">Ваша дополнительная экономия: %s</div></div>', wc_price($sale_price - $lp_sale_price));

            $sale_price = $text;
        }

        return $sale_price;
    }

    /**
     * Получим все вариации товара
     *
     * @param $product
     * @return array
     */
    public function getVariation($product)
    {
        $variations = [];

        foreach($product->get_available_variations() as $variation) {
            $variations[$variation['variation_id']] = $variation;
        }

        return $variations;
    }

    public function getVisibleTags()
    {
        return [
            'new-arrival',
            //'ss19',
            //'ss20'
        ];
    }

    public function sortProductListByTags($posts)
    {
        $exclude = [
            1159, // special-offer
            1158, // new-arrival
        ];

        $terms = get_terms(
            array(
                'taxonomy' => 'product_tag',
                'hide_empty' => false,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'exclude' => $exclude
            ) );

        $arr0 = [];
        foreach($terms as $term) {
            $arr0[] = $term->slug;
        }

        //$arr0 = ['ss20', 'ss19'];
        $arr1 = [];
        $arr2 = [];

        foreach($posts as $post) {
            $tags = get_the_terms( $post->ID, 'product_tag' );//wc_get_product_tag_list( $post->ID );

            if($tags) {
                foreach($tags as $tag) {
                    if(in_array($tag->slug, $arr0)) {
                        $arr1[$tag->slug][] = $post;
                    } else {
                        $arr2[] = $post;
                    }
                }
            } else {
                $arr2[] = $post;
            }
        }

        if(!empty($arr1)) {

            $posts = [];

            foreach ($arr0 as $tag_slug) {
                if (isset($arr1[$tag_slug])) {
                    $posts = array_merge($posts, $arr1[$tag_slug]);
                }
            }

            $posts = array_merge($posts, $arr2);
        }

        return $posts;
    }

    /**
     * Есть ли товары, помеченные NewArrival
     *
     * @return bool
     */
    public function isNewArrivalTagProducts()
    {
        $args = array(
            'product_tag' => 'new-arrival',
            'post_type' 	 => 'product'
        );

        $recent_posts = new WP_Query( $args );

        return $recent_posts->have_posts() ? true : false;
    }

    public function getRelatedProducts()
    {
        $arr = array(

            /*woman*/
            'bleyzery'  => ['kurtki', 'tolstovkisvitshotyhudi', 'rubashki', 'bryukishorty'],
            'kurtki'    => ['bleyzery', 'tolstovkisvitshotyhudi', 'rubashki', 'bryukishorty'],
            'plashhi'   => ['plashchi', 'tolstovkisvitshotyhudi', 'rubashki', 'bryukishorty'],
            'plashchi'  => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', 'rubashki', 'bryukishorty'],

            'trikotaj'         => ['verhnyaya-odejda', 'trikotaj', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            /*'kardigany'         => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            'puloverydjempery'  => ['verhnyaya-odejda', 'kardigany', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            'futbolki'          => ['verhnyaya-odejda', 'puloverydjempery', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            'tolstovkisvitshotyhudi' => ['verhnyaya-odejda', 'puloverydjempery', 'bryukishorty', 'polo-trikotaj'],
            'kurtki-trikotaj'   => ['verhnyaya-odejda', 'polo-trikotaj', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            'polo-trikotaj'     => ['verhnyaya-odejda', 'kurtki-trikotaj', 'bryukishorty', 'tolstovkisvitshotyhudi'],*/

            'platyayubki'       => ['verhnyaya-odejda', 'trikotaj', 'obuv', 'aksessuary'],
            'platya'            => ['verhnyaya-odejda', 'trikotaj', 'obuv', 'aksessuary'],
            'yubki'             => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', 'polo-trikotaj', 'obuv'],

            'rubashki'          => ['verhnyaya-odejda', 'puloverydjempery', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            /*'oblegayushchiy-kroy' => ['verhnyaya-odejda', 'puloverydjempery', 'bryukishorty', 'tolstovkisvitshotyhudi'],
            'bluzki'            => ['verhnyaya-odejda', 'puloverydjempery', 'bryukishorty', 'tolstovkisvitshotyhudi'],*/

            'bryukishorty'              => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', 'trikotaj', 'obuv'],
            'bryuki-bryukishorty'       => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', ['polo-trikotaj', 'rubashki', 'futbolki'], 'obuv'],
            'kombinezony-bryukishorty'       => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', ['polo-trikotaj', 'rubashki', 'futbolki'], 'obuv'],
            'djinsy'                    => ['verhnyaya-odejda', 'tolstovkisvitshotyhudi', ['polo-trikotaj', 'rubashki', 'futbolki'], 'obuv'],

            'obuv' => ['verhnyaya-odejda', 'trikotaj', 'rubashki', 'bryukishorty'],

            'aksessuary' => ['kosmetichki', ['sharfy', 'galstuki'], 'sumkiryukzaki', 'koshelki-aksessuary'],

            /* man */
            'verhnyaya-odejda-mujchiny' => ['verhnyaya-odejda-mujchiny', 'trikotaj-mujchiny', 'rubashki-mujchiny', 'bryukishorty-mujchiny'],

            'trikotaj-mujchiny' => ['verhnyaya-odejda-mujchiny', 'trikotaj-mujchiny', 'bryukishorty-mujchiny', 'obuv-man'],

            'rubashki-mujchiny' => ['verhnyaya-odejda-mujchiny', ['kardigany-trikotaj-mujchiny','puloverydjempery-trikotaj-mujchiny'], 'bryukishorty-mujchiny', 'obuv-man'],

            'bryukishorty-mujchiny' => ['verhnyaya-odejda-mujchiny', 'trikotaj-mujchiny', 'rubashki-mujchiny', 'obuv-man'],

            'obuv-man' => ['verhnyaya-odejda-mujchiny', 'trikotaj-mujchiny', 'rubashki-mujchiny', 'bryukishorty-mujchiny'],
            'aksessuary-mujchiny' => [['koshelki','derjateli-dlya-kart'], 'kepki', 'remni', 'galstuki-aksessuary-mujchiny'],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],

            /*man*/
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],
            '' => ['', '', '', ''],

        );



        return $arr;
    }
}