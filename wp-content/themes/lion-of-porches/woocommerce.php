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
                <?php
                $current_user = wp_get_current_user();
                $user_discount = (new Crm())->getUserDiscount($current_user->user_email);

                if(!$user_discount) {
                return false;
                }

                $user_discount_level = (new Crm())->getUserLevel()[$user_discount];
                /*echo 'level='.$user_discount_level;
                (new Helper())->dump($current_user);*/

                ?>

                <div class="user-discount-level">
                    <span class="user-name"><?=$current_user->display_name?></span>
                    <span class="user-level"><?=$user_discount_level?></span>
                    <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>.jpg">
                </div>

                <?php
                woocommerce_breadcrumb();
                ?>
               <?php woocommerce_content();?>
            </div>
		</div>
	</div>
</section>
<?php get_footer(); // подключаем footer.php ?>
