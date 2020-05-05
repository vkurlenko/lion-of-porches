<?php
$args = array(
    //'theme_location'  => , // область темы
    'menu'            => 'bottom-menu-2', // какое меню нужно вставить (по порядку: id, ярлык, имя)
    /*'container'       => 'div', // блок, в который нужно поместить меню, укажите false, чтобы не помещать в блок
    'container_class' => 'menu-{menu slug}-container', // css-класс блока
    'container_id'    => , // id блока
    'menu_class'      => 'menu', // css-класс меню
    'menu_id'         => , // id меню
    'echo'            => true, // вывести или записать в переменную
    'fallback_cb'     => 'wp_page_menu', // какую функцию использовать если меню не существует, укажите false, чтобы не использовать ничего
    'before'          => , // текст или html-код, который нужно вставить перед каждым <a>
    'after'           => , // после </a>
    'link_before'     => , // текст перед анкором ссылки
    'link_after'      => , // после анкора и перед </a>
    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>', // HTML-шаблон
    'depth'           => 0 // количество уровней вложенности*/
);
wp_nav_menu($args);
?>

<!--<ul>
    <li><a href="/stores/">Магазины</a></li>
    <li><a href="/carrers/">Вакансии</a></li>
    <li><a href="/rights/">Правовая информация</a></li>
    <li><a href="/contacts/">Контакты</a></li>
</ul>-->