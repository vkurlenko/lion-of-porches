<div class="product-card-personal-inner">

    <?php

    global $product;

    $h = new Helper();
    $wh = new WooHelper();
    $crm = new Crm();
    $levels = $crm->getUserLevel();

    //$h->dump($product);

    $arr = [
        'user_name' => 'Гость',
        'user_level' => '',
        'user_sum' => 0,
        'user_next_level_sum' => 0,
    ];

    $current_user = wp_get_current_user();
    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        /*echo 'first name: '       . $current_user->user_firstname . '<br />';
        echo 'last name: '        . $current_user->user_lastname  . '<br />';*/

        $arr['user_name'] = $current_user->display_name;//$current_user->user_firstname.' '.$current_user->user_lastname;

        $crm_user = $crm->getCrmUser();

        if($crm_user) {
            $arr['user_level'] = isset($crm_user->discount) ? $crm_user->discount : 0;
            $arr['user_sum'] = (int)$crm_user->comment;
        } else {
            $arr['user_level'] = 0;
            $arr['user_sum'] = '';
        }

        //$h->dump($crm->getCrmUser());
    }
    ?>



    <?php
    // учетная запись клиента в CRM
    $crm_user = (new Crm())->getCrmUser();

    // текущий уровень клиента
    $user_discount_level = isset($levels[$arr['user_level']]) ? $levels[$arr['user_level']] : 0;

    // сумма покупок для перехода на след. уровень
    $next_level_up_sum = (int)$crm->getNextLevelUpSum($arr['user_sum']) - (int)$arr['user_sum'];

    // скидка следующего уровня
    $next_level_discount = $crm->getLevelBySum((int)$crm->getNextLevelUpSum($arr['user_sum']));

    // название следующего уровня
    $next_level_name = $levels[$next_level_discount];

    $price_tpl = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₽</span><span class="val">%s</span></span>';

    if($user_discount_level):
        ?>
        <span class="user-name"><?=$arr['user_name']?>,</span>
        <!--<div class="user-discount-level">
            <span class="user-level"><?/*=$user_discount_level*/?></span>
            <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?/*=strtolower($user_discount_level)*/?>.jpg">
            <div style="clear: both"></div>
        </div>-->
        <div class="status">
            <?php
            if($crm_user->card):
            ?>
                <span><?=sprintf('Номер Вашей карты: <strong>%s</strong>', sprintf("%013d", $crm_user->card))?></span>
            <?php
            endif;
            ?>
            <span><?=sprintf('Ваше накопление: <span class="user-sum">%s</span>', wc_price($arr['user_sum']))?></span>
            <span><?=sprintf('Ваш статус: <span class="user-level">%s</span>', $user_discount_level)?></span>
            <div class="user-level-img">
                <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>.jpg">
            </div>
        </div>

        <!--<div>
            <span><?/*=sprintf('Сумма покупок до следующего уровня <span class="level-name">%s (-%s%%)</span>: %s', $next_level_name, $next_level_discount, wc_price($next_level_up_sum));*/?></span>
        </div>-->

        <div class="special-price">
            <div><?=sprintf('Ваша дополнительная скидка: <span class="level-name">-<span class="data-personal-discount">%s</span>%%</span>', '' )?></div>
            <div><?=sprintf('Итого для Вас: <span class="level-name"><span class="data-personal-price">'.$price_tpl.'</span></span>', '' )?></div>
            <!--<div><?/*=sprintf('Ваша экономия <span class="level-name"><span class="data-personal-economy">'.$price_tpl.'</span></span>', '' )*/?></div>-->
        </div>


    <?php
    else:?>
        <?=get_template_part( 'parts/guest-block');?>
    <?php
    endif;
    ?>
    <div class="product">
        <div class="page-sep">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAVCAMAAABxCz6aAAAAeFBMVEVMaXHeChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChreChpoOs6ZAAAAJ3RSTlMA8OCgMMBQEIBgcJALA0ywDHyXCIhAtQEuPeICavcYEnmHHg7ECqhJSmy5AAAAoklEQVR42m3Q2RKDIAwFUEpZq1ar3fc9//+HvYbMUGvvQyYcogwo5YkoVcQrTsbFPGNZE8X7Q2fkYNRx7fFijOEfXL8wovLojG7OYWFtA9RWMAUtMKj/OEFOv4i+LYqi7TEgtaCXK0iA2OyAXWBYYrlSnNcUGdw9fzvGGOORaF0maax90tvJns9jOHGM5izdrkpYyVtpvefBQQjZHsboN6n9AL9IGjFswE2+AAAAAElFTkSuQmCC" alt="Lion of Porches">
        </div>
    </div>
    <div class="offerta">
        <a href="/programma-privilegij/" target="_blank">Условия и лимиты программы привилегий</a>
    </div>
</div>