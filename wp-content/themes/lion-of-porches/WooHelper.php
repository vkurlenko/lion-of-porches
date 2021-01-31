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
    public $attr;
    public $index_article;
    const CART_CONTENTS_COUNT_MAX = 5;

    /**
     *  Загрузка каталога товаров из текстового файла
     */
    public function createVarProductsFromFile($filename, $num = 5)
    {
        $this->index_article = 6;

        $f = file($filename);
        $num_articles = count($this->getAllArticles($f));
        //$num = count($f);

        echo sprintf('<p class="alert alert-primary">Найдено %s строк, %s артикулов</p>', count($f), $num_articles);

        $completed = [];
        $process = [];

        // создадим массив артикулов
        $arr = [];

        $i = 0;

        $articles = [];

        foreach($f as $str) {

            if($i == 0) {
                $i++;
                continue;
            }

            // пропустим пустые строки
            if(trim($str) == '') {
                continue;
            }


            // разберем строку на элементы массива
            //Женщины|Верхняя одежда|Блейзеры||5604205986388|L101052038|Blazer|Blazer|Блейзер|L101052038 M 580 SS20|M|синий|62% полиэстер 37% иск. шелк 1% эластан|Португалия|L101052038_580_1 (2)|23 290|Джинсовый .||
            /*
            0           1                       2               3               4                   5                   6           7           8               9                   10                              11          12      13          14                                      15                      16              17      18          19
            Коллекция	НоменклатурнаяГруппа	ГруппаТоваров	Подлинии	    ТоварнаяКатегория	Штрихкод	        Артикул	    Ссылка	    Наименование	НаименованиеПолное	Характеристика				    Код цвета	Размер	ЦветРус	    Состав	                                Страна происхождения	ФайлКартинки	price   description	Остаток

            FW20	    Женщины	                Верхняя одежда	Блейзеры		                    5604206041611	    L101054013	Outerwear	Blazer	        Блейзер	            L101054013	 L	 560	 FW20	560	        L	    голубой	    64% полиэстер 33% вискоза 3% эластан	Португалия		                        22590

            0	FW20;
            1 	Женщины;
            2 	Верхняя одежда;
            3 	Блейзеры;
            4 	;
            5 	5604206041611;
            6 	L101054013;
            7 	Outerwear;
            8 	Blazer;
            9 	Блейзер;
            10 	L101054013;
            11 	L;
            12 	560;
            13 	FW20;
            14 	560;
            15 	L;
            16 	голубой;
            17	64% полиэстер 33% вискоза 3% эластан;
            18	Португалия;
            19	;
            20 	22590;
            21	;
            */


            $item = explode(';', $str);

            foreach ($item as &$v) {
                $v = trim($v);
            }

            // с 1 по 4 элемент - дерево названий категорий
            $item_tree = array_slice($item, 1, 4);

            // создадим дерево категорий и получим массив ID категорий товара
            $parent_cats = $this->createCategoryTree($item_tree, 0);

            if($this->get_product_by_sku($item[6])) {
                if (!in_array($item[6], $completed)) {
                    $completed[] = $item[6];
                }

                continue;
            }

            if (!in_array($item[6], $articles)) {
                $articles[] = $item[6];
                $process[] = $item[6];
            }

            // добавим позицию в массив с привязкой к артикулу
            $arr[$item[6]][] = [
                'tag'           => $item[0],  // метка
                'cat'           => $parent_cats,//$item_tree,
                'sku'           => $item[6],  // артикул
                'post_title'    => $item[9],  // название
                'post_excerpt'  => $item[9],  // полное название
                'size'          => $item[11], // размер
                'color'         => $item[16], // цвет (рус.)
                'material'      => $item[17], // материал
                'vendor'        => $item[18], // страна
                'price'         => $item[20], // цена
                'post_content'  => $item[21], // описание
                'stock'         => $item[22], // остатки
                'data'          => [$item[6], $item[11], $item[14], $item[13]]
            ];

            if(!isset($arr[$item[6]]['size'])) {
                $arr[$item[6]]['size'] = [];
            }

            if(!in_array($item[11], $arr[$item[6]]['size'])) {
                $arr[$item[6]]['size'][] = $item[11];
            }

            if(!isset($arr[$item[6]]['color'])) {
                $arr[$item[6]]['color'] = [];
            }

            if(!in_array($item[16], $arr[$item[6]]['color'])) {
                $arr[$item[6]]['color'][] = $item[16];
            }

            if (count($articles) > ($num - 1)) {
                break;
            }
        }

        //echo sprintf('Осталось %s строк<br>', $i);

        $this->attr = $this->getAttributes();

        //echo sprintf('<p class="alert alert-secondary">Загружены ранее: %s</p>', implode(', ', $complited));
        echo sprintf('<p class="alert alert-secondary">Загружены ранее: %s</p>', implode(', ', $this->completedLinks($completed)));
        echo sprintf('<p class="alert alert-success">Обработаны: %s</p>', implode(', ', $process));


        echo '<table class="table table-striped table-sm table-bordered">
              <thead>
                <tr>
                  <th scope="col">ID товара</th>
                  <th scope="col">Артикул</th>
                  <th scope="col">Цвет</th>
                  <th scope="col">Размер</th>
                  <th scope="col">Цена</th>
                </tr>
              </thead>
              <tbody>';

        // создадим вариативные товары
        foreach($arr as $sku) {
            $this->createVarProduct($sku); //die;
        }
        echo '</tbody></table>';
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

        $attr = $this->attr;//$this->getAttributes();

        //$this->dump($attr); die;

        $post = array(
            'post_title'   => $data['post_title'],//"Product with Variations2",
            'post_content' => $description, //$data['post_content'],//"product post content goes here…",
            'post_status'  => "publish",
            'post_excerpt' => $data['post_excerpt'],//"product excerpt content…",
            //'post_name'    => $data['post_name'],//"test_prod_vars2", //name/slug
            'post_type'    => "product"
        );

        if(!$this->get_product_by_sku($data['sku'])) {
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

        // метка товара (сезон)
        wp_set_object_terms( $new_post_id, $data['tag'], 'product_tag');

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

            if(!isset($attr['size'][mb_strtolower($variation['size'])])) {
                $new_attr_size = mb_strtolower($variation['size']);

                echo 'нет атрибута '.$new_attr_size.'<br>';

                if(!term_exists( $new_attr_size, 'pa_size' ) ){
                    wp_insert_term( $new_attr_size, 'pa_size' );
                } else {
                    $term   = get_term_by( 'name', $new_attr_size, 'pa_size' );
                    $variation['size'] = $term->name;
                }

                $attr = $this->getAttributes();
            }

            $variation_size = $attr['size'][mb_strtolower($variation['size'])];

            if(!isset($attr['color'][mb_strtolower($variation['color'])])) {
                $new_attr_color = mb_strtolower($variation['color']);

                echo 'нет атрибута '.$new_attr_color.'<br>';

                if(!term_exists( $new_attr_color, 'pa_color' ) ){
                    wp_insert_term( $new_attr_color, 'pa_color' );
                } else {
                    $term   = get_term_by( 'name', $new_attr_color, 'pa_color' );
                    $variation['color'] = $term->name;
                }

                $attr = $this->getAttributes();
            }

            $variation_color = $attr['color'][mb_strtolower($variation['color'])];
            $variation_sku = trim($variation['sku']).'.'.trim($variation['data'][2]);
            $variation_descr = sprintf ('Страна производства: %s<br>Материал: %s<br>%s', $variation['vendor'], $variation['material'], $variation['post_content']);
            $variation_stock = intval($variation['stock']) ? intval($variation['stock']) : 0;

            echo sprintf('<tr><th scope="row"><a href="%s" target="_blank">%s</a></th><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', get_permalink( $new_post_id ), $new_post_id, $variation_sku, $variation_color, $variation_size, $variation['price']);/* вариация 1 */

            $my_post = array(
                'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
                'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
                'post_status' => 'publish',
                'post_parent' => $new_post_id,//post is a child post of product post
                'post_type' => 'product_variation',//set post type to product_variation
                'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
            );

            //if(!$this->get_product_by_sku($variation_sku)) {
            //Create product/post:
            wp_insert_post( $my_post );
            /*} else {
                $variation_id = $this->get_product_by_sku($variation_sku);
            }*/

            update_post_meta($variation_id, 'attribute_pa_size', $variation_size);
            update_post_meta($variation_id, 'attribute_pa_color',  $variation_color);
            update_post_meta($variation_id, '_price',  $variation['price']);
            update_post_meta($variation_id, '_regular_price', $variation['price']);
            update_post_meta($variation_id, '_sale_price', '');
            update_post_meta($variation_id, '_variation_description', $variation_descr);
            update_post_meta($variation_id, '_sku', $variation_sku);
            update_post_meta($variation_id, '_stock_status', $variation_stock ? 'instock' : 'outofstock');
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

        //$this->dump($attr); die;

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
        if(!$product->is_in_stock()) {
            return;
        }
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

                    <h3 class="woocommerce-loop-product__title"><?=$product->get_name()?></h3>

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
        //$arr0 = ['ss19', 'ss20'];
        $arr0 = ['fw21', 'fw20', 'fw19', 'fw18', 'fw17', 'fw16', 'ss20', 'ss19', 'ss18', 'ss17', 'ss16'];
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

    /*public function sortProductListByTags($posts)
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
    }*/

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

    public function dump($obj)
    {
        echo "<pre>".print_r((object)$obj, true)."</pre>";
    }

    /**
     * Найти ID продукта по его артикулу
     *
     * @param $sku
     * @return null|string
     */
    public function get_product_by_sku($sku) {

        global $wpdb;

        $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

        if ( $product_id ) return $product_id;

        return null;
    }

    /**
     * Выбор всех уникальных артикулов из файла импорта
     *
     * @param $f
     * @return array
     */
    public function getAllArticles($f)
    {
        $arr = [];

        foreach ($f as $str) {
            $item = explode(';', $str);

            foreach ($item as &$v) {
                $v = trim($v);
            }

            if (!in_array($item[$this->index_article], $arr)) {
                $arr[] = $item[$this->index_article];
            }
        }

        return $arr;
    }

    public function completedLinks($completed)
    {
        $s = [];

        foreach ($completed as $article) {
            $id = $this->get_product_by_sku($article);
            $link = $id ? '<a href="'.get_permalink( $id ).'" target="_blank">'.$article.'</a>'  : $article;

            $s[] = $link;
        }

        return $s;
    }

    public function insertSalePriceField()
    {
        return;
        global $wpdb;

        //$arr = [19213,19214,19215,19217,19218,19219,19221,19225,19227,19228,19229,19231,19235,19237,19239,19240,19242,19243,19245,19246,19248,19249,19250,19251,19252,19253,19254,19255,19257,19259,19261,19263,19265,19267,19269,19270,19271,19273,19274,19276,19277,19278,19280,19281,19282,19283,19285,19287,19289,19290,19292,19294,19295,19296,19298,19299,19301,19302,19304,19306,19308,19309,19310,19312,19314,19315,19316,19318,19319,19320,19321,19323,19324,19325,19326,19327,19328,19330,19331,19333,19334,19335,19336,19337,19343,19344,19346,19347,19348,19353,19354,19355,19356,19357,19358,19359,19360,19361,19362,19364,19366,19368,19369,19370,19372,19373,19374,19375,19377,19378,19380,19381,19382,19383,19385,19386,19387,19388,19390,19391,19392,19393,19395,19396,19398,19399,19401,19403,19405,19407,19408,19409,19411,19413,19414,19416,19418,19421,19423,19425,19427,19429,19430,19431,19433,19434,19435,19437,19438,19439,19441,19442,19443,19444,19445,19447,19449,19450,19452,19453,19454,19456,19457,19459,19460,19461,19462,19463,19465,19466,19467,19468,19469,19471,19472,19474,19475,19476,19478,19479,19480,19481,19482,19484,19486,19487,19488,19489,19491,19493,19494,19495,19496,19497,19498,19499,19501,19502,19504,19505,19506,19508,19510,19512,19513,19514,19515,19522,19523,19524,19525,19527,19529,19531,19532,19534,19535,19536,19537,19539,19541,19542,19543,19544,19546,19547,19548,19549,19551,19552,19553,19554,19555,19557,19558,19559,19560,19562,19564,19566,19568,19569,19571,19573,19574,19576,19578,19580,19582,19583,19584,19586,19588,19590,19591,19593,19595,19597,19599,19601,19603,19605,19606,19607,19608,19610,19611,19613,19614,19615,19617,19618,19619,19621,19622,19623,19625,19626,19627,19628,19630,19631,19632,19633,19634,19635,19637,19639,19641,19642,19643,19645,19647,19649,19651,19652,19653,19654,19655,19657,19659,19664,19666,19667,19668,19669,19670,19672,19673,19677,19678,19679,19685,19697,19698,19700,19702,19704,19706,19708,19710,19712,19714,19715,19717,19719,19723,19725,19726,19728,19730,19732,19734,19736,19737,19738,19739,19741,19742,19744,19746,19755,19758,19759,19761,19763,19765,19767,19769,19771,19772,19774,19776,19777,19778,19780,19782,19784,19785,19786,19787,19789,19790,19791,19792,19793,19795,19796,19797,19798,19799,19801,19803,19805,19807,19808,19809,19811,19812,19813,19815,19816,20258];

        $i = 1;
        foreach ($arr as $post_id) {
            echo $i++.') post_id = '.$post_id.' = '.update_post_meta( $post_id,'_sale_price', '').'<br>';
        }
    }
}