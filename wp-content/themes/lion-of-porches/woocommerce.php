<?php
/**
 * Template Name: Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>

<section>
	<div class="container">
		<div class="row">
            <div class="col-md-12 content">

               <?php woocommerce_content();?>
            </div>
		</div>
	</div>
</section>
<?php get_footer(); // подключаем footer.php ?>
