<?php
/**
 * Шаблон подвала (footer.php)
 * @package WordPress
 * @subpackage your-clean-template-3
 */
?>

<section class="pitchbar">
    <div class="container">
        <?php get_template_part( 'parts/footer-info-blocks');?>
    </div>
</section>

<footer>
    <div class="container">
        <div class="row">
            <div class="footer-item lion col-xs-6 col-md-2">
                <h5>Lion of Porches</h5>

                <?php get_template_part( 'parts/bottom-menu-1');?>

            </div>

            <div class="footer-item loja col-xs-6 col-md-2"><h5>Покупателям</h5>
                <?php get_template_part( 'parts/bottom-menu-2');?>
            </div>

            <div class="footer-item pagamento col-xs-6 col-md-2">
                <h5>Способы оплаты</h5>
                <ul class="gateways list-inline">
                    <li><img src="/wp-content/themes/lion-of-porches/img/visa_PNG30.png" alt="Visa"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/mc.png" alt="Master Card"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/logo-mir-2.png" alt="МИР"></li>
                    <!--<li><img src="/wp-content/themes/lion-of-porches/img/icons8-apple-pay-128-2.png" alt="Apple Pay"></li>-->
                    <li><img src="/wp-content/themes/lion-of-porches/img/pay.png" alt="Samsung Pay"></li>
                    <!--<li><img src="/wp-content/themes/lion-of-porches/img/paypal.png" alt="PayPal"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/amex.png" alt="Amex"></li>-->
                </ul>

                <h5>Доставка</h5>
                <ul class="gateways list-inline delivery">
                    <li><img src="/wp-content/themes/lion-of-porches/img/sdek2.png" alt="СДЭК"></li>
                </ul>
            </div>

            <div class="footer-item apoio col-xs-6 col-md-3"><h5>Поддержка</h5>
                <ul class="gateways list-inline">
                    <li>Email: <a href="mailto:online@lion-of-porches.ru">online@lion-of-porches.ru</a></li>
                    <li>Телефон: <a href="tel:+74993766636" rel="nofollow">+7 (499) 376 66 36</a></li>
                    <li>Whatsapp: <a href="https://api.whatsapp.com/send?phone=79268046934%26text=%26source=%26data=" rel="nofollow">+7 (926) 804 69 34</a></li>
                    <li>Рабочие дни пн-пт с 10.00 до 22.00</li>
                </ul>
            </div>

            <div class="footer-item medias col-xs-12 col-md-2"><h5>Присоединяйтесь к нам</h5>
                <ul class="list-inline">
                    <li><a href="https://www.facebook.com/groups/lionofporches/?ref=share" target="_blank" rel="nofollow"><!--<i class="fa fa-facebook" aria-hidden="true"></i>--><img src="/wp-content/themes/lion-of-porches/img/fb.png" alt="facebook"></a></li>
                    <li><a href="https://instagram.com/lionofporches_russia_official?igshid=187kvo1awgsmb" target="_blank" rel="nofollow"><!--<i class="fa fa-instagram"></i>--><img src="/wp-content/themes/lion-of-porches/img/insta.png" alt="instagram"></a></li>
                    <li><a href="https://www.youtube.com/c/LionOfPorchesLondon/featured" target="_blank" rel="nofollow"><!--<i class="fa fa-youtube"></i>--><img src="/wp-content/themes/lion-of-porches/img/you.png" alt="youtube"></a></li>
                    <li><a href="https://vk.com/lionofporches1" target="_blank" rel="nofollow"><!--<i class="fa fa-vk"></i>--><img src="/wp-content/themes/lion-of-porches/img/vk.png" alt="vk"></a></li>
                </ul>
                <!--<div class="newsletter"><a href="#"><i class="fa fa-envelope-o"></i>Subscribe our newsletter</a></div>-->
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="container"><p class="pull-left">
                2020 © Lion of Porches <span>|</span>All rights reserved
            </p>
            <div class="pull-right">
                <ul class="list-inline">
                    <li><a href="http://www.vtex.com" class="vtex" rel="nofollow" target="_blank"><i
                                    class="iconlion-vtex"></i></a></li>
                    <li><a href="http://www.loba.pt" class="vtex" rel="nofollow" target="_blank"><i
                                    class="iconlion-loba"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

	<!--<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php /*$args = array( // опции для вывода нижнего меню, чтобы они работали, меню должно быть создано в админке
						'theme_location' => 'bottom', // идентификатор меню, определен в register_nav_menus() в function.php
						'container'=> false, // обертка списка, false - это ничего
						'menu_class' => 'nav nav-pills bottom-menu', // класс для ul
				  		'menu_id' => 'bottom-nav', // id для ul
				  		'fallback_cb' => false
				  	);
					wp_nav_menu($args); // выводим нижние меню
					*/?>
				</div>
			</div>
		</div>
	</footer>-->
<?php wp_footer(); // необходимо для работы плагинов и функционала  ?>
</body>
</html>