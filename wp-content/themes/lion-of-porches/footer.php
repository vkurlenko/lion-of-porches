<?php
/**
 * Шаблон подвала (footer.php)
 * @package WordPress
 * @subpackage your-clean-template-3
 */
?>

<section class="pitchbar">
    <div class="container">
        <div class="col-xs-12 col-md-4"><h4>Бесплатная доставка</h4>
            <p>Равным образом консультация с широким активом требуют определения и уточнения модели развития. Товарищи! сложившаяся структура организации представляет собой интересный эксперимент проверки направлений прогрессивного развития. Товарищи! постоянное информационно-пропагандистское обеспечение нашей деятельности позволяет выполнять важные задания по разработке модели развития.

                </p>
        </div>
        <div class="col-xs-12 col-md-4"><h4>Возврат</h4>
            <p>Идейные соображения высшего порядка, а также дальнейшее развитие различных форм деятельности позволяет оценить значение новых предложений. Равным образом постоянный количественный рост и сфера нашей активности играет важную роль в формировании системы обучения кадров, соответствует насущным потребностям..</p>
        </div>
        <div class="col-xs-12 col-md-4"><h4>Безопасная покупка</h4>
            <p>С другой стороны рамки и место обучения кадров способствует подготовки и реализации модели развития. Не следует, однако забывать, что дальнейшее развитие различных форм деятельности способствует подготовки и реализации форм развития.</p>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="row">
            <div class="footer-item lion col-xs-6 col-md-2"><h5>Lion of Porches</h5>
                <ul>
                    <li><a href="/stores/">Магазины</a></li>
                    <li><a href="/carrers/">Вакансии</a></li>
                    <li><a href="/rights/">Правовая информация</a></li>
                    <li><a href="/contacts/">Контакты</a></li>
                </ul>
            </div>
            <div class="footer-item loja col-xs-6 col-md-2"><h5>Онлайн магазин</h5>
                <ul>
                    <li><a href="/promo/">Промоакции</a></li>
            </div>
            <div class="footer-item pagamento col-xs-6 col-md-3"><h5>Способы оплаты</h5>
                <ul class="gateways list-inline">
                    <li><img src="/wp-content/themes/lion-of-porches/img/visa.png" alt="Visa"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/mastercard.png" alt="Master Card"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/multibanco.png" alt="Multibanco"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/paypal.png" alt="PayPal"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/amex.png" alt="Amex"></li>
                </ul>
                <h5>Доставка</h5>
                <ul class="gateways list-inline delivery">
                    <li><img src="/wp-content/themes/lion-of-porches/img/dhl.png" alt="DHL"></li>
                    <li><img src="/wp-content/themes/lion-of-porches/img/chronopost.png" alt="Chronopost"></li>
                </ul>
            </div>
            <div class="footer-item apoio col-xs-6 col-md-2"><h5>Поддержка</h5>
                <ul class="gateways list-inline">
                    <li><a href="mailto:mail@lion-of-porches.ru">Email: mail@lion-of-porches.ru</a></li>
                    <li>Телефон: +7 495 123-45-67</li>
                    <li><a href="https://api.whatsapp.com/send?phone=351910046621%26text=%26source=%26data=">Whatsapp:
                            +7 903 123-45-67 (text)</a></li>
                    <li>Рабочие дни пн-пт с 9.00 до 18.00</li>
                </ul>
            </div>
            <div class="footer-item medias col-xs-12 col-md-3"><h5>Присоединяйтесь к нам</h5>
                <ul class="list-inline">
                    <li><a href="#" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" target="_blank"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="#" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="#" target="_blank"><i class="fa fa-pinterest"></i></a></li>
                    <li><a href="#" target="_blank"><i class="fa fa-instagram"></i></a></li>
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