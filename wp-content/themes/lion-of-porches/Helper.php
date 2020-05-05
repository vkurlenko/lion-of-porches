<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26.04.2020
 * Time: 17:30
 */

class Helper
{
    public function getTopCategory()
    {
        $args = array(
            'taxonomy' => 'product_cat',
            'orderby'    => 'term_order',// ‘orderby’ => ‘term_order’
            'order'      => 'ASC',
            'hide_empty' => false,
            'hierarchical' => false,
            'parent' => 0,
            'exclude' => 22
        );

        $product_categories = get_terms( $args );

        return $product_categories;
    }

    public function getCategoryImage($category,  $size = null)
    {
        if(!$category) {
            return false;
        } else {
            $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
            $image = wp_get_attachment_image( $thumbnail_id, $size );
        }

        return $image;
    }

    public function dump($obj)
    {
        echo "<pre>".print_r((object)$obj, true)."</pre>";
    }

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

        return $arr;
    }

    public function createVarProductsFromFile()
    {
        //$this->dump(get_terms("pa_color")); die;

        $f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog.txt');

        // создадим массив артикулов
        $arr = [];

        foreach($f as $str) {

            if(trim($str) == '') {
                continue;
            }

            // разберем строку на элементы массива
            $item = explode('|', $str);

            // с 0 по 3 элемент - дерево ID категорий
            $item_tree = array_slice($item, 0, 4);

            // создадим дерево катгорий и получим ID категории вариативного товара
            $parent_cat = $this->createCategoryTree($item_tree, 0);

            // добавим позицию в массив с привязкой к артикулу
            $arr[$item[5]][] = [
                'cat'           => $item_tree,
                'sku'           => $item[5],
                'post_title'    => $item[7],
                'post_excerpt'  => $item[8],
                'size'          => $item[10],
                'color'         => $item[11],
                'price'         => $item[13],
                'post_content'  => $item[14]
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
        }

        //$this->dump($arr);

        foreach($arr as $sku) {
            $this->createVarProduct($sku); //die;
        }

        $this->dump($arr); die;
    }

    //public function createVarProduct($cats = [25], $data = [])
    public function createVarProduct($sku = [])
    {
        global $wpdb;

        $data = $sku[0];
        $cats = $data['cat'];
        $size = $sku['size'];
        $color = $sku['color'];

        $attr = $this->getAttributes();

        $this->dump($attr);

        $post = array(
            'post_title'   => $data['post_title'],//"Product with Variations2",
            'post_content' => $data['post_content'],//"product post content goes here…",
            'post_status'  => "publish",
            'post_excerpt' => $data['post_excerpt'],//"product excerpt content…",
            //'post_name'    => $data['post_name'],//"test_prod_vars2", //name/slug
            'post_type'    => "product"
        );

        //Create product/post:
        $new_post_id = wp_insert_post( $post, false );
        //$logtxt = "PrdctID: $new_post_id\n";

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
        update_post_meta( $new_post_id, '_sku', $data['sku']);
        update_post_meta( $new_post_id, '_visibility', 'visible' );

        $i = 1;
        $variation_id = $new_post_id + 1;
        foreach($sku as $variation) {

            if(key($variation) == 'size' || key($variation) == 'color') {
                continue;
            }

            //$variation['size'] = strtolower($variation['size']);
            $variation_size = $attr['size'][mb_strtolower($variation['size'])];
            $variation_color = $attr['color'][mb_strtolower($variation['color'])];
            //$this->dump($variation_color); die;

            echo sprintf('post_id: %s, SKU %s: color %s, size %s, price %s <br>', $new_post_id, $variation['sku'], $variation_color, $variation_size, $variation['price']);/* вариация 1 */


            $my_post = array(
                'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
                'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
                'post_status' => 'publish',
                'post_parent' => $new_post_id,//post is a child post of product post
                'post_type' => 'product_variation',//set post type to product_variation
                'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
            );

            $attID = wp_insert_post( $my_post );

            $variation_id++;



            update_post_meta($variation_id, 'attribute_pa_size', $variation_size);
            update_post_meta($variation_id, 'attribute_pa_color',  $variation_color);
            update_post_meta($variation_id, '_price',  $variation['price']);
            update_post_meta($variation_id, '_regular_price', $variation['price']);
            update_post_meta($variation_id, '_description', $variation['post_content']);

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

           /* echo $variation_id.'<br>';
            $this->dump($thedata);*/
            update_post_meta( $variation_id,'_product_attributes',$thedata);
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
        foreach($tree as $level) {
            //echo $level.'<br>';

            $cat_id = $this->SearchCat ($level);
            //echo $cat_id.'<br>';
            //$this->dump($cat_id);

            if($cat_id === false) {
                $this->dump($cat_id);
                //echo ' создание категории ' . $level.' parent_id = '. $ParentCatID.'<br>';
                $cat_id = $this->createCategory($level, $ParentCatID);
            } else {
                $ParentCatID = $cat_id;
            }

            $ParentCatID = $cat_id;
            //echo $ParentCatID.'<br>';
        }

        return $cat_id;
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
        $cat_defaults = array(
            //'cat_ID' => $CatID,                // ID категории, которую нужно обновить. 0 - добавит новую категорию.
            'cat_name' => $CatName,             // название категории. Обязательный.
            'category_description' => '', // описание категории
            'category_nicename' => '',      // слаг категории
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
     * Возвращает Wordpress id  категории, если найдена и false, если нет
     *
     * @param $Cat
     * @return array|bool|false|WP_Term
     */
    public function SearchCat ($Cat)
    {
        $cat_id = get_term_by( 'name', $Cat, 'product_cat', 'OBJECT', 'raw' );

        //$this->dump($cat_id);

        if (isset($cat_id->term_id)) {
            return $cat_id->term_id;
        }
        else {
            return false;
        }
    }

    public function createVarProduct3($cats = [25], $data = [])
    {
        global $wpdb;
            //$cats = array(25);
            $insertLog = "insert_product_logs.txt";//name the log file in wp-admin folder

        $data = [
            'post_title' => 'test name',
            'post_content' => 'test_description',
            'post_excerpt' => 'краткое описание',
            'post_name'    => "test_product_slug", //name/slug

            'size' => 'XL',
            'color' => 'blue',
            'price' => '150',
            'sku' => 'test_sku'
        ];

        $post = array(
            'post_title'   => $data['post_title'],//"Product with Variations2",
            'post_content' => $data['post_content'],//"product post content goes here…",
            'post_status'  => "publish",
            'post_excerpt' => $data['post_excerpt'],//"product excerpt content…",
            'post_name'    => $data['post_name'],//"test_prod_vars2", //name/slug
            'post_type'    => "product"
        );

        //Create product/post:
        $new_post_id = wp_insert_post( $post, false );
        $logtxt = "PrdctID: $new_post_id\n";

        //make product type be variable:
        wp_set_object_terms ($new_post_id,'variable','product_type');
        //add category to product:
        wp_set_object_terms( $new_post_id, $cats, 'product_cat');

        //################### Add size attributes to main product: ####################

        //Array for setting attributes

        // размеры
        $avail_attributes = array(
            'XL',
            'L',
            'M',
            'S',
            'XS'
        );
        wp_set_object_terms($new_post_id, $avail_attributes, 'pa_size');

        // цвета
        $avail_attributes2 = array(
            'red',
            'green',
            'blue'
        );
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
        //########################## Done adding attributes to product #################

        //set product values:
        //update_post_meta( $new_post_id, '_stock_status', 'instock');
        //update_post_meta( $new_post_id, '_weight', "0.06" );
        update_post_meta( $new_post_id, '_sku', $data['post_title']);
        //update_post_meta( $new_post_id, '_stock', "100" );
        update_post_meta( $new_post_id, '_visibility', 'visible' );

        //###################### Add Variation post types for sizes #############################
        //insert 5 variations post_types for 2xl, xl, lg, md, sm:
        //$i = 1;

        /*$num = count($avail_attributes) * count($avail_attributes2);
        while ($i <= $num) {//while creates 5 posts(1 for ea. size variation 2xl, xl etc):*/

            $my_post = array(
                'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
                //'post_title'=> 'Variation for prdct#'. $new_post_id,
                'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
                'post_status' => 'publish',
                'post_parent' => $new_post_id,//post is a child post of product post
                'post_type' => 'product_variation',//set post type to product_variation
                'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
            );

            //Insert ea. post/variation into database:
            $attID = wp_insert_post( $my_post );

            $logtxt .= "Attribute inserted with ID: $attID\n";
            //set IDs for product_variation posts:
            $variation_id = $new_post_id + 1;
            /*$variation_two = $variation_id + 1;
            $variation_three = $variation_two + 1;
            $variation_four = $variation_three + 1;
            $variation_five = $variation_four + 1;*/

           //Create 2xl variation for ea product_variation:
            update_post_meta($variation_id, 'attribute_pa_size', 'xl');
            update_post_meta($variation_id, 'attribute_pa_color',  'red');
            update_post_meta($variation_id, '_price',  $data['price']);
            update_post_meta($variation_id, '_regular_price', $data['price']);

            //add size attributes to this variation:
            wp_set_object_terms($variation_id, $avail_attributes, 'pa_size');
            //wp_set_object_terms($variation_id, $avail_attributes2, 'pa_color');

            $thedata = Array(
                'pa_color'=>Array(
                    'name'=>'red',
                    'value'=>'',
                    'is_visible' => '1',
                    'is_variation' => '1',
                    'is_taxonomy' => '1'
                ),
                'pa_size'=>Array(
                    'name'=> 'xl',
                    'value'=>'',
                    'is_visible' => '1',
                    'is_variation' => '1',
                    'is_taxonomy' => '1'
                )
            );

            update_post_meta( $variation_id,'_product_attributes',$thedata);

            /*////////////////////////////*/
        $my_post = array(
            'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
            //'post_title'=> 'Variation for prdct#'. $new_post_id,
            'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
            'post_status' => 'publish',
            'post_parent' => $new_post_id,//post is a child post of product post
            'post_type' => 'product_variation',//set post type to product_variation
            'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
        );
        wp_insert_post( $my_post );

        $variation_id = $variation_id + 1;

        update_post_meta($variation_id, 'attribute_pa_size', 'xl');
        update_post_meta($variation_id, 'attribute_pa_color',  'blue');
        update_post_meta($variation_id, '_price',  350);
        update_post_meta($variation_id, '_regular_price', 350);

        //add size attributes to this variation:
        wp_set_object_terms($variation_id, $avail_attributes, 'pa_size');
        //wp_set_object_terms($variation_id, $avail_attributes2, 'pa_color');

        $thedata = Array(
            'pa_color'=>Array(
                'name'=>'blue',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ),
            'pa_size'=>Array(
                'name'=> 'xl',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            )
        );

        update_post_meta( $variation_id,'_product_attributes',$thedata);

        /*$thedata = Array(
            /*'pa_size'=>Array(
                'name'=> $data['size'],
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ),*/
            /*'pa_color'=>Array(
                'name'=>$data['color'],
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ));

            update_post_meta( $variation_id,'_product_attributes',$thedata);*/



            /*//Create xl variation for ea product_variation:
            update_post_meta( $variation_two, 'attribute_pa_size', 'xl');
            update_post_meta( $variation_two, '_price', 20.99 );
            update_post_meta( $variation_two, '_regular_price', '20.99');
            //add size attributes:
            wp_set_object_terms($variation_two, $avail_attributes, 'pa_size');
            $thedata = Array('pa_size'=>Array(
                'name'=>'xl',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ));
            update_post_meta( $variation_two,'_product_attributes',$thedata);

            //Create lg variation for ea product_variation:
            update_post_meta( $variation_three, 'attribute_pa_size', 'lg');
            update_post_meta( $variation_three, '_price', 18.99 );
            update_post_meta( $variation_three, '_regular_price', '18.99');
            wp_set_object_terms($variation_three, $avail_attributes, 'pa_size');
            $thedata = Array('pa_size'=>Array(
                'name'=>'lg',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ));
            update_post_meta( $variation_three,'_product_attributes',$thedata);

            //Create md variation for ea product_variation:
            update_post_meta( $variation_four, 'attribute_pa_size', 'md');
            update_post_meta( $variation_four, '_price', 18.99 );
            update_post_meta( $variation_four, '_regular_price', '18.99');
            wp_set_object_terms($variation_four, $avail_attributes, 'pa_size');
            $thedata = Array('pa_size'=>Array(
                'name'=>'md',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ));
            update_post_meta( $variation_four,'_product_attributes',$thedata);

            //Create sm variation for ea product_variation:
            update_post_meta( $variation_five, 'attribute_pa_size', 'sm');
            update_post_meta( $variation_five, '_price', 18.99 );
            update_post_meta( $variation_five, '_regular_price', '18.99');
            wp_set_object_terms($variation_five, $avail_attributes, 'pa_size');
            $thedata = Array('pa_size'=>Array(
                'name'=>'sm',
                'value'=>'',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'
            ));
            update_post_meta( $variation_five,'_product_attributes',$thedata);*/

            //$i++;
        //}
    }

    /****************************************************/

    public function createProduct2()
    {
        $product = new WC_Product();
        $this->dump($product);
        $product->set_sku( '1234' );
        $product->set_parent_id(38);
        $product->save();
        $this->dump($product);die;
        $product->set_parent_id(38);
        $product->set_sku('111-222-nnn');
        //$product->set_menu_order(4);
        $product->save();
    }

    public function createProduct()
    {
        //echo 'createProduct';
        $post = array(
            'post_author' => 1,
            'post_content' => 'Описание товара	', //Описание товара
            'post_status' => "publish",
            'post_title' => "Мой товар", // Название товара
            'post_type' => "product",
        );
        $post_id = wp_insert_post($post); //Создаем запись

        wp_set_object_terms($post_id, 38, 'product_cat'); //Задаем категорию товара

        /*$puthUpload = wp_upload_dir();
        $PhotoProd = "mainimg.jpg";
        //Картинка
        if($PhotoProd){
            $PhotoProd = trim($PhotoProd);
            $PhotoProd = $puthUpload["baseurl"]."/productimg/images/".$PhotoProd;
            $thumbid = media_sideload_image($PhotoProd, $post_id, $desc = null, $return = 'id');

            set_post_thumbnail($post_id, $thumbid);
        }

        $PhotosProd = "img1.jpg,img2.jpg,img3.jpg";
        //Доп. картинка
        if($PhotosProd){
            $arPhotosProd = explode(",",$PhotosProd);

            foreach($arPhotosProd as $key=>$Item){
                if($Item){
                    $Item = trim($Item);
                    $Item = $puthUpload["baseurl"]."/productimg/images/".$Item;
                    $imgID[$key] = media_sideload_image($Item, $post_id, $desc = null, $return = 'id');
                }
            }
            update_post_meta( $post_id, '_product_image_gallery', implode(", ", $imgID));
        }*/

        update_post_meta($post_id, '_sku', 123); //Артикул
        update_post_meta( $post_id, '_visibility', 'visible' ); // Видимость: открыто
        //update_post_meta( $post_id, 'total_sales', '0');   //Создается произвольное поле
        update_post_meta( $post_id, '_downloadable', 'no'); //Не скачиваемый
        update_post_meta( $post_id, '_virtual', 'no'); //Не виртуальный

        wp_set_object_terms($post_id, "variable", 'product_type');

        $VariationAttribute = "Цвет";
        $VariationAttributesValue[] = "Красный";
        $VariationAttributesValue[] = "Желтый";
        $VariationAttributesValue[] = "Зеленый";
        $PriceVariation["Красный"] = 100;
        $PriceVariation["Желтый"] = 200;
        $PriceVariation["Зеленый"] = 350;

        $this->add_variation_product($post_id,$VariationAttribute,$VariationAttributesValue,$PriceVariation);


    }

    public function add_variation_product( $post_id, $select_attributes, $select_attribute_terms, $PriceVariation)
    {
        /*
        $select_attributes -  атрибут по которому у нас будет вариация
        $select_attribute_terms - значения атрибутов для вариации
        */

        $product_attributes = $select_attributes; //Атрибут

        $attributes = wc_attribute_taxonomy_name( $product_attributes );
        $pa_attr = 'pa_' . $product_attributes;
        wp_set_object_terms( $post_id, $select_attribute_terms, $pa_attr );

        $thedata = array( $pa_attr => array(
            'name' => $pa_attr,
            'value' => '',
            'postion' => '0',
            'is_visible' => '1',
            'is_variation' => '1',
            'is_taxonomy' => '1'
        ) );
        update_post_meta( $post_id, '_product_attributes', $thedata );

        foreach($select_attribute_terms as $key => $attribute_term)
        {
            $variation = array(
                'post_title'   => 'Product #' . $post_id . ' Variation',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_parent'  => $post_id,
                'post_type'    => 'product_variation'
            );

            $variation_id = wp_insert_post( $variation );

            if(!$variation_id){
                echo "Ошибка создания вариации
";
            }else{
                echo "Вариация создана
";
            }

            update_post_meta( $variation_id, '_regular_price', $PriceVariation[$attribute_term] );
            update_post_meta( $variation_id, '_price', $PriceVariation[$attribute_term] );

            update_post_meta( $variation_id, 'attribute_' . $attributes, $attribute_term );
        }

        WC_Product_Variable::sync( $post_id );
    }
}