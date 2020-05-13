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

    public function dump($obj)
    {
        echo "<pre>".print_r((object)$obj, true)."</pre>";
    }
}