<?php
/**
 * Template Name: FrontPage (front-page.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>
<section>
    <div class="main">

        <!-- big banner -->
        <div class="container-fluid">
            <div class="row">
                <div class="big-banner">
                    <img class="hidden-xs" src="/wp-content/themes/lion-of-porches/img/promoOPT3.jpg">
                    <img class="visible-xs" src="/wp-content/themes/lion-of-porches/img/promoOMEN2.jpg">
                </div>

                <div class="big-banner-text">
                    <ul class="btn-list list-inline">
                        <li><a class="btn-alt" href="#">Женщины</a></li>
                        <li><a class="btn-alt" href="#">Мужчины</a></li>
                        <li><a class="btn-alt" href="#">Девочки</a></li>
                        <li><a class="btn-alt" href="#">Мальчики</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /big banner -->

        <!-- video -->
        <div class="container-fluid">
            <!--<div class="row video">
                <video loop="" autoplay="" preload="auto" muted="" id="video_inicial">
                    <source src="https://imagens.lionofporches.pt/videos/ditch2.mp4" type="video/mp4">
                </video>
            </div>-->
        </div>
        <!-- /video -->

        <!-- delimiter -->
        <div class="page-sep col-xs-12">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAVCAMAAABxCz6aAAAAeFBMVEVMaXHeChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChpoOs6ZAAAAJ3RSTlMA8OCgMMBQEIBgcJALA0ywDHyXCIhAtQEuPeICavcYEnmHHg7ECqhJSmy5AAAAoklEQVR42m3Q2RKDIAwFUEpZq1ar3fc9//+HvYbMUGvvQyYcogwo5YkoVcQrTsbFPGNZE8X7Q2fkYNRx7fFijOEfXL8wovLojG7OYWFtA9RWMAUtMKj/OEFOv4i+LYqi7TEgtaCXK0iA2OyAXWBYYrlSnNcUGdw9fzvGGOORaF0maax90tvJns9jOHGM5izdrkpYyVtpvefBQQjZHsboN6n9AL9IGjFswE2+AAAAAElFTkSuQmCC" alt="Lion of Porches">
        </div>
        <!-- /delimiter -->

        <!-- product category -->
        <div class="container product-category-list">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <a href="#"><img src="/wp-content/themes/lion-of-porches/img/woman.jpg"></a>
                </div>
                <div class="col-md-6 col-xs-12">
                    <a href="#"><img src="/wp-content/themes/lion-of-porches/img/man.jpg"></a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <a href="#"><img src="/wp-content/themes/lion-of-porches/img/boy.jpg"></a>
                </div>
                <div class="col-md-6 col-xs-12">
                    <a href="#"><img src="/wp-content/themes/lion-of-porches/img/girl.jpg"></a>
                </div>
            </div>
        </div>
        <!-- /product category -->

        <!-- lifeStyle -->


        <div class="container lifestyle">

            <h2>#lionofporches</h2>

            <div class="row">
                <div class="page-sep col-xs-12">
                    <p>See how others are living the <br>
                        Lion of Porches lifestyle</p>
                </div>
            </div>

            <div class="row instagram-gallery">
                <div class="col-md-4">
                    <div class="col col-1">
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                    </div>
                    <div class="col col-2">
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col col-3">
                        <div class="row-1">
                            <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                            <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        </div>

                        <div class="row-2">
                            <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col col-4">
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                    </div>
                    <div class="col col-5">
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                        <div style="background-image: url(/wp-content/themes/lion-of-porches/img/1.jpg);"><a href="#"></a></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /lifeStyle -->

    </div>
</section>
<?php get_footer(); // подключаем footer.php ?>