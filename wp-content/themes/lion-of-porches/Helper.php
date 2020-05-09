<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26.04.2020
 * Time: 17:30
 */

class Helper
{
    public function getTopCategory($parent = 0)
    {
        $args = array(
            'taxonomy' => 'product_cat',
            'orderby'    => 'term_order',// ‘orderby’ => ‘term_order’
            'order'      => 'ASC',
            'hide_empty' => false,
            'hierarchical' => false,
            'parent' => $parent,
            'exclude' => 22
        );

        $product_categories = get_terms( $args );

        return $product_categories;
    }

    public function getSabCategoryTree($parent = 0)
    {
        $args = array(
            'show_option_all'    => '',
            'show_option_none'   => __('No categories'),
            'orderby'            => 'name',
            'order'              => 'ASC',
            'style'              => 'list',
            'show_count'         => 0,
            'hide_empty'         => 1,
            'use_desc_for_title' => 1,
            'child_of'           => $parent,
            'feed'               => '',
            'feed_type'          => '',
            'feed_image'         => '',
            'exclude'            => '',
            'exclude_tree'       => '',
            'include'            => '',
            'hierarchical'       => true,
            'title_li'           => '',
            'number'             => NULL,
            'echo'               => 1,
            'depth'              => 0,
            'current_category'   => 0,
            'pad_counts'         => 0,
            'taxonomy'           => 'product_cat',
            'walker'             => 'Walker_Category',
            'hide_title_if_empty' => true,
            'separator'          => '<br />',
        );

        echo '<ul>';
        wp_list_categories( $args );
        echo '</ul>';
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

        //$this->dump($arr); die;

        return $arr;
    }

    public function createVarProductsFromFile()
    {
        return;

        $f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog_full.txt');
        //$f = file($_SERVER['DOCUMENT_ROOT'].'/temp/catalog_mini.txt');

        // создадим массив артикулов
        $arr = [];

        foreach($f as $str) {

            if(trim($str) == '') {
                continue;
            }

            // разберем строку на элементы массива
            $item = explode('|', $str);
            $item_data = explode(' ', $item[9]);

            // с 0 по 3 элемент - дерево ID категорий
            $item_tree = array_slice($item, 0, 4);

            // создадим дерево катгорий и получим ID категории вариативного товара
            //echo $this->dump($item_tree).'<br>';
            $parent_cat = $this->createCategoryTree($item_tree, 0);

            // добавим позицию в массив с привязкой к артикулу
            $arr[$item[5]][] = [
                'cat'           => $item_tree,
                //'sku'           => $item[5].'.'.$item_data[2],
                'sku'           => $item[5],
                'post_title'    => $item[7],
                'post_excerpt'  => $item[8],
                'size'          => $item[10],
                'color'         => $item[11],
                'price'         => $item[13],
                'post_content'  => $item[14],
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

        foreach($arr as $sku) {
            $this->createVarProduct($sku); //die;
        }

        //$this->dump($arr); die;
    }

    //public function createVarProduct($cats = [25], $data = [])
    public function createVarProduct($sku = [])
    {
        global $wpdb;

        //$this->dump($sku); return; //die;

        $data = $sku[0];
        $cats = $data['cat'];
        $size = $sku['size'];
        $color = $sku['color'];
        $color_code = $data['data'][2];
        //echo $color_code.'<br>';

        $attr = $this->getAttributes();

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
        update_post_meta( $new_post_id, '_sku', $data['sku'].'.'.$data['data'][2]);
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
            $variation_sku = $variation['sku'].'.'.$variation['data'][2];
            $variation_descr = $variation['post_content'];
            //$this->dump($variation_color); die;

            echo sprintf('post_id: %s, SKU %s: color %s, size %s, price %s <br> %s', $new_post_id, $variation_sku, $variation_color, $variation_size, $variation['price'], $variation_descr);/* вариация 1 */


            $my_post = array(
                'post_title'=> 'Variation #' . time() . ' for prdct#'. $new_post_id,
                'post_name' => 'product-' . $new_post_id . '-variation-',// . $i,
                'post_status' => 'publish',
                'post_parent' => $new_post_id,//post is a child post of product post
                'post_type' => 'product_variation',//set post type to product_variation
                'guid'=>home_url() . '/?product_variation=product-' . $new_post_id . '-variation-' . time()
            );

            $attID = wp_insert_post( $my_post );

            update_post_meta($variation_id, 'attribute_pa_size', $variation_size);
            update_post_meta($variation_id, 'attribute_pa_color',  $variation_color);
            update_post_meta($variation_id, '_price',  $variation['price']);
            update_post_meta($variation_id, '_regular_price', $variation['price']);
            update_post_meta($variation_id, '_description', $variation['post_content']);
            update_post_meta($variation_id, '_sku', $variation_sku);

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
        //echo 'create category '.$CatName.' ' .$ParentCatID.'<br>';
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
    public function SearchCat ($Cat, $ParentCatID)
    {
        if($Cat == '') {
            return $ParentCatID;
        }

        //echo $ParentCatID;

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


/*echo $product_categories[0]->term_id;
        $this->dump($product_categories); die;*/


        if (isset($product_categories[0]->term_id)) {
            if($product_categories[0]->parent == $ParentCatID) {
                return $product_categories[0]->term_id;
            }
        }
        else {
            return false;
        }
    }
}