<div class="row visible-xs">
    <div class="main-menu mobile hidden">

        <ul>
            <li><a href="/catalog/">Женщины</a></li><li><a href="/catalog/">Мужчины</a></li><li><a href="/catalog/">Девочки</a></li><li><a href="/catalog/">Мальчики</a></li>
        </ul>

        <form>
            <div class="row open-search mobile">
                <div class="col-xs-6"><input type="text" value="" placeholder="поиск"></div>
                <div class="col-xs-6  btn-search"><input type="submit" value="Найти" class="btn btn-primary btn-submit"></div>
                <!--<span class="glyphicon glyphicon-search" aria-hidden="true"></span>-->
            </div>
        </form>

        <div class="row">
            <div class="col-xs-6">
                <?
                $args = array(
                    'menu'            => 'top-menu-1', // какое меню нужно вставить (по порядку: id, ярлык, имя)
                );
                wp_nav_menu($args);?>
                <!--<ul>
                    <li><a href="/delivery/">Доставка</a></li><li><a href="/about/">О компании</a> </li>
                </ul>-->
            </div>
            <div class="col-xs-6">
                <ul>
                    <li><a href="/catalog/">Каталоги</a></li><li><a href="/forpartners/">Партнерам</a></li><li><a href="#">Войти</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>