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
            'orderby'    => 'count',
            'order'      => 'DESC',
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
        echo "<pre>".print_r($obj, true)."</pre>";
    }
}