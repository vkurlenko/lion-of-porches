<?php
/**
 * Template Name: Шаблон программа привилегий (loyalty.php)
 * @package WordPress
 * @subpackage LionOfPorches
 */
get_header(); // подключаем header.php ?>


<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12  content">
                <?php get_template_part( 'parts/user-level-block');?>
                <?php
                /* $post = get_post();
                 var_dump($post);*/
                ?>

                <div class="row">
                    <div class="col-md-8 col-md-offset-2 loyalty">
                        <h2><?=$post->post_title;?></h2>

                        <!-- block 1 -->
                        <section class="block-1">
                            <img src="/wp-content/themes/lion-of-porches/img/loyalty1.jpg">
                        </section>

                        <!-- block 2 -->
                        <section class="block-2 row">
                            <h3 class="right">Как стать участником</h3>
                            <div class="col-md-5 col-md-offset-1">
                                Для получения дисконтной карты достаточно сделать любую разовую покупку и заполнить небольшую анкету.<br>
                                Дисконтная карта является накопительной, т.е. все покупки суммируются и процент скидки зависит от накоплений.<br><br>

                                <p>* скидки указаны за товар, продающийся за полную стоимость.</p>
                                <p>** величина скидки не распространяется на товары, участвующие в акциях или сезонных распродажах.</p>
                                <p>*** величина скидки не распространяется в период действия подарочной скидки на день рождения.</p>
                                <p>**** скидка по программе привилегий предоставляется в магазинах Lion Of Porches в Москве и на сайте www.lion-of-porches.ru, информацию о программе привилегии в других городах уточняйте у сотрудников магазинов.</p>

                            </div>
                            <div class="col-md-5 col-md-offset-1">
                                <table cellspacing="1" border="1">
                                    <tr>
                                        <th>Накопленная сумма  покупок</th>
                                        <th>Категория карты</th>
                                        <th>%  скидки по карте</th>
                                    </tr>
                                    <tr>
                                        <td>От 10,000 руб.</td>
                                        <td>Basic</td>
                                        <td>5%</td>
                                    </tr>
                                    <tr>
                                        <td>Больше 70,000 руб.</td>
                                        <td>Silver</td>
                                        <td>10%</td>
                                    </tr>
                                    <tr>
                                        <td>Больше 150,000 руб.</td>
                                        <td>Gold</td>
                                        <td>15%</td>
                                    </tr>
                                    <tr>
                                        <td>Больше 250,000 руб.</td>
                                        <td>Platinum</td>
                                        <td>20%</td>
                                    </tr>
                                    <tr>
                                        <td>Больше 350,000 руб.</td>
                                        <td>Signature</td>
                                        <td>25%</td>
                                    </tr>
                                    <tr>
                                        <td>Больше 500,000 руб.</td>
                                        <td>Infinity</td>
                                        <td>30%</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="bg"></div>
                        </section>

                        <!-- block 3 -->
                        <section class="block-3 row">
                            <div class="col-md-6 col-md-offset-6">
                                <h3>Спасибо за лояльность</h3>
                                <p>Для наших постоянных покупателей мы добавили дополнительную скидку, которую можно накопить во время наших распродаж и акций:</p>
                            </div>
                            <div class="bg"></div>
                        </section>

                        <section class="block-31 row">
                            <div class="col-md-6 col-md-offset-6">
                                <table cellspacing="1" border="1">
                                    <tr>
                                        <th>cкидка</th>
                                        <th>категория карты</th>
                                        <th>дополнительная <br>скидка к цене</th>
                                    </tr>
                                    <tr>
                                        <td rowspan="6">-10%</td>
                                        <td>Basic</td>
                                        <td>0%</td>
                                    </tr>
                                    <tr>
                                        <td>Silver</td>
                                        <td>1%</td>
                                    </tr>
                                    <tr>
                                        <td>Gold</td>
                                        <td>7%</td>
                                    </tr>
                                    <tr>
                                        <td>Platinum</td>
                                        <td>13%</td>
                                    </tr>
                                    <tr>
                                        <td>Signature</td>
                                        <td>19%</td>
                                    </tr>
                                    <tr>
                                        <td>Ultra</td>
                                        <td>25%</td>
                                    </tr>

                                    <tr>
                                        <td rowspan="6">-20%</td>
                                        <td>Basic</td>
                                        <td>0%</td>
                                    </tr>
                                    <tr>
                                        <td>Silver</td>
                                        <td>2%</td>
                                    </tr>
                                    <tr>
                                        <td>Gold</td>
                                        <td>3%</td>
                                    </tr>
                                    <tr>
                                        <td>Platinum</td>
                                        <td>4%</td>
                                    </tr>
                                    <tr>
                                        <td>Signature</td>
                                        <td>10%</td>
                                    </tr>
                                    <tr>
                                        <td>Ultra</td>
                                        <td>18%</td>
                                    </tr>

                                    <tr>
                                        <td rowspan="6">-30%</td>
                                        <td>Basic</td>
                                        <td>2%</td>
                                    </tr>
                                    <tr>
                                        <td>Silver</td>
                                        <td>3%</td>
                                    </tr>
                                    <tr>
                                        <td>Gold</td>
                                        <td>4%</td>
                                    </tr>
                                    <tr>
                                        <td>Platinum</td>
                                        <td>6%</td>
                                    </tr>
                                    <tr>
                                        <td>Signature</td>
                                        <td>7%</td>
                                    </tr>
                                    <tr>
                                        <td>Ultra</td>
                                        <td>8%</td>
                                    </tr>
                                </table>

                                <span>*таблица 2</span>
                            </div>
                            <div class="bg"></div>
                        </section>

                        <!-- block-4 -->
                        <section class="block-4 row">
                            <div class="col-md-6">
                                <h3>Условия</h3>

                                <ul>
                                    <li>Дисконтная карта начинает действовать не ранее чем через сутки с момента совершения покупки и заполнения всех обязательных полей анкеты постоянного покупателя.</li>
                                    <li>Вся информация, занесенная в анкету, является конфиденциальной и разглашению не подлежит. (<a href="/politika-privatnosti/">политика конфиденциальности</a>)</li>
                                    <li>Дисконтная карта является безвременной, т.е. срок ее действия не ограничен.</li>
                                    <li>В случае возврата товара начисленные за покупку бонусы и накопления снимаются.</li>
                                    <li>Подарочная скидка в размере 20% можно воспользоваться как в день Вашего рождения, так и за две недели до и после него.</li>
                                    <li>Для владельцев наших карт доп. скидка в день рождения будет действовать в соответствии с таблицей 2*.</li>
                                </ul>
                            </div>

                            <div class="bg"></div>

                        </section>

                        <!-- block-5 -->
                        <section class="block-5 row">
                            <h3 class="left">Закрытые распродажи</h3>
                            <div class="col-md-8">
                                <img src="/wp-content/themes/lion-of-porches/img/facade.jpg">
                            </div>
                            <div class="col-md-4">
                                <p>Участникам программы привилегий отрыт доступ к участию в наших закрытых распродажах и акциях</p>
                            </div>
                        </section>

                        <!-- block-6 -->
                        <section class="block-6 row">

                            <img src="/wp-content/themes/lion-of-porches/img/catwalk.jpg">

                            <div class="over">
                                <h3 class="right">Возможность посещать показы</h3>

                                <div class="col-md-5 col-md-offset-6">
                                    <p>Самые привилегированные друзья получат возможность участия  в мероприятиях
                                        и посещать показы сезонных коллекций в Португалии.
                                    </p>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<?php get_footer(); // подключаем footer.php ?>
