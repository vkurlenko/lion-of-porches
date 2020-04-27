<?php
/**
 * Template Name: Шаблон Магазины (stores.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>
<!--sdfgsdfgsdfgsdfgsdfg
<section>
    <div class="container">
        <div class="row">
            <div class="<?php /*content_class_by_sidebar(); // функция подставит класс в зависимости от того есть ли сайдбар, лежит в functions.php */?>">
                <h1><?php /*// заголовок архивов
                    if (is_day()) : printf('Daily Archives: %s', get_the_date()); // если по дням
                    elseif (is_month()) : printf('Monthly Archives: %s', get_the_date('F Y')); // если по месяцам
                    elseif (is_year()) : printf('Yearly Archives: %s', get_the_date('Y')); // если по годам
                    else : 'Archives';
                    endif; */?></h1>
                <?php /*if (have_posts()) : while (have_posts()) : the_post(); // если посты есть - запускаем цикл wp */?>
                    <?php /*get_template_part('loop'); // для отображения каждой записи берем шаблон loop.php */?>
                <?php /*endwhile; // конец цикла
                else: echo '<p>Нет записей.</p>'; endif; // если записей нет, напишим "простите" */?>
                <?php /*pagination(); // пагинация, функция нах-ся в function.php */?>
            </div>
            <?php /*get_sidebar(); // подключаем sidebar.php */?>
        </div>
    </div>
</section>-->


<section>
    <!-- slick-slider-stores -->
    <div class="container-fluid slider-stores">
        <?php
        //$posts = query_posts('category=stores');
        $posts = query_posts('category__in=4');

        if($posts):?>
        <div class="slider1">
            <?php
            $size = [1860, 963];
            foreach($posts as $post):?>
                <div><?=get_the_post_thumbnail( $post->id, $size, array('class' => 'alignleft') )?></div>
           <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
    <!-- /slick-slider-stores -->

    <div class="container">
        <div class="row  stores-qd-v1-wrapper">
            <div class="col-md-6 hidden-xs" style="height: 500px;">
                <div id="map" style="height: 100%; width: 100%; margin-top: 106px"></div>
            </div>

            <div class="col-md-6">

                <!-- список адресов -->
                <?php
                $terms = get_terms( 'category', 'parent=5' );
                $locations = '';

                if($terms) {
                    foreach ($terms as $term) :
                        $posts = query_posts( 'category__in='.$term->term_id );
                        ?>
                        <div class="stores-qd-v1-geo-list">
                            <div class="stores-qd-v1-list-title">
                                <h4><!--<span class="qd-count"><?/*=count($posts)*/?></span> магазин в городе --><?=$term->name?></h4>
                            </div>
                            <div class="stores-qd-v1-stores-geo-locations-wrapper storelocator-panel">
                                <ul class="store-list">

                                    <?php

                                    foreach($posts as $post):
                                    ?>
                                    <li class="store" id="store-1">
                                        <div class="store">
                                            <div class="title"><?=$post->post_title?></div>
                                            <div class="address"><?=$post->post_content?></div>
                                            <div class="phone"><?=get_post_meta( $post->ID, 'store-phone', true )?></div>
                                        </div>
                                    </li>
                                    <?php

                                    /* координаты магазинов */

                                    if(get_post_meta( $post->ID, 'store-location', true )) {
                                        $location = explode(',', get_post_meta( $post->ID, 'store-location', true ));
                                        if(count($location) == 2) {
                                            $lat = $location[0];
                                            $lng = $location[1];
                                            $locations .= "{lat: $lat, lng: $lng},";
                                        }
                                    }
                                    /* /координаты магазинов */

                                    endforeach;
                                    ?>

                                </ul>
                            </div>
                        </div>
                    <?php
                    endforeach;
                }
                ?>
                <!---->

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
    <script>

        function initMap() {

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,//10,
                center: {lat: 55.752939, lng: 37.619889} //55.752939, 37.619889
            });

            // Create an array of alphabetical characters used to label the markers.
            var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

            // Add some markers to the map.
            // Note: The code uses the JavaScript Array.prototype.map() method to
            // create an array of markers based on a given "locations" array.
            // The map() method here has nothing to do with the Google Maps API.
            var markers = locations.map(function(location, i) {
                return new google.maps.Marker({
                    position: location,
                    label: labels[i % labels.length]
                });
            });

            // Add a marker clusterer to manage the markers.
            var markerCluster = new MarkerClusterer(map, markers,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        }
        var locations = [
            <?=$locations?>
            /*{lat: -31.563910, lng: 147.154312},
            {lat: -33.718234, lng: 150.363181},
            {lat: -33.727111, lng: 150.371124},
            {lat: -33.848588, lng: 151.209834},
            {lat: -33.851702, lng: 151.216968}*/

        ]
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBq-CwR0J447ACl-SiKAmJgPW_1Z3Q1MNM&callback=initMap" type="text/javascript"></script>
</section>


<?php get_footer(); // подключаем footer.php ?>
