<?php
/**
 * Template Name: Шаблон New Arrival (new-arrival.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header();

?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 content">

                <h2><?=$post->post_title;?></h2>

                <?php
                $url = parse_url ( $_SERVER['REQUEST_URI']);

                if(isset($url['query'])) {
                    $q = explode('=', $url['query']);

                    if(!empty($q)) {
                        $tag = $q[1];

                        if($tag) {
                            echo do_shortcode('[products tag="new-arrival" category="'.$tag.'"]');
                        }
                    }
                }
                ?>

            </div>
        </div>
    </div>
</section>

<?php get_footer();



