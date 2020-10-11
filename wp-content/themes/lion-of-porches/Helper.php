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
            'hide_empty' => true,
            'hierarchical' => false,
            'parent' => $parent,
            'exclude' => 22
        );

        $product_categories = get_terms( $args );

        return $product_categories;
    }

    public function getSabCategoryTree($parent = 0, $depth = 0)
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
            'depth'              => $depth,
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

    /**
     * Транслитерация РУС -> LAT
     *
     * @param $s
     * @return mixed|null|string|string[]
     */
    public function translit($s)
    {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус

        return $s; // возвращаем результат
    }

    public function wp_get_post_by_slug( $slug, $post_type = 'post', $unique = true )
    {
        $args=array(
            'name' => $slug,
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => 1
        );
        $my_posts = get_posts( $args );
        if( $my_posts ) {
            //echo 'ID on the first post found ' . $my_posts[0]->ID;
            if( $unique ){
                return $my_posts[ 0 ];
            }else{
                return $my_posts;
            }
        }
        return false;
    }

    public function getSizeGuideTab($category)
    {
        $posts = query_posts('category_name='.$category);

        $html = '<div class="vertical-tab" role="tabpanel"><ul class="nav nav-tabs" role="tablist">';

        $i = 0;
        foreach($posts as $post) {
            $html .= sprintf('<li class="%s"><a data-toggle="tab" href="#%s">%s</a></li>', (!$i ? 'active' : ''), $post->post_name, $post->post_title);
            $i++;
        }

        $html .= '</ul>';

        $html .= '<div class="tab-content">';

        $i = 0;
        foreach($posts as $post) {
            $html .= sprintf('<div id="%s" class="tab-pane fade %s">', $post->post_name, (!$i ? 'in active' : ''));
            //$html .= sprintf('<h3>%s</h3>', $post->post_title);
            $html .= $post->post_content;
            $html .= '</div>';
            $i++;
        }

        $html .= '</div></div>';

        return $html;
        
    }

    public function dump($obj)
    {
        echo "<pre>".print_r((object)$obj, true)."</pre>";
    }

    public function writeToFile($filename = '', $data)
    {
        //if(empty($filename)) {
            $filename = time().'.txt';
        //}

        //if(is_array($data)) {
            $data = serialize($data);
            //file_put_contents($filename, $data);
        //} else {
            $f = fopen($filename, 'w+');
            fwrite($f, $data);
        //}
    }

    /**
     * Вывод дерева категорий товара по его ID
     * в виде Категория/подкатегория/...
     *
     * @param $product_id
     * @return string|WP_Error
     */
    public function getProductCategoriesById($product_id)
    {
        $category = '';
        $taxonomy = 'product_cat' ;
        $terms = get_the_terms( $product_id, 'product_cat', $taxonomy );

        if ( !empty( $terms ) ) {
            $links = array();

            foreach ( $terms as $term ) {
                $link = get_term_link( $term, $taxonomy );
                if ( is_wp_error( $link ) ) {
                    return $link;
                }
                $links[] = $term->name;
            }

            $category = implode('/', $links);
        }

        return $category;
    }


    /**
     * Печать js-объекта для кода Яндекс.Метрики Покупка
     *
     * @param $order
     * @return string
     */
    public function getOrderItemsForYM($order)
    {
        $jsObject = [];

        foreach ( $order->get_items() as $item_id => $item )
        {
            // получим товар
            $product = wc_get_product( $item->get_product_id() );

            // получим все вариации товара
            $variations = (new WooHelper())->getVariation($product);

            $item_price = '';
            $item_sku = $product->get_sku();

            foreach($variations as $key => $val) {
                if($key == $item->get_variation_id()) {
                    $item_price = $val['display_regular_price'];
                    $item_sku = $val['sku'];
                    break;
                }
            }

            $obj = "\r\n".'{';

            $color = get_term_by('slug', $item->get_meta('pa_color'), 'pa_color' )->name;
            $size = get_term_by('slug', $item->get_meta('pa_size'), 'pa_size' )->name;

            $obj .= sprintf('"id": "%s",', $item_sku);
            $obj .= sprintf('"name": "%s",', $item->get_name());
            $obj .= sprintf('"price": "%s",', $item_price);
            $obj .= sprintf('"brand": "%s",', 'Lion of Porches');
            $obj .= sprintf('"category": "%s",', $this->getProductCategoriesById($item->get_product_id()));
            $obj .= sprintf('"variant": "%s",', implode(', ', [$color, $size]));
            $obj .= sprintf('"quantity": "%s",', $item->get_quantity());

            $obj .= '}';

            $jsObject[] = $obj;
        }

        return implode(',', $jsObject);
    }

}