<?php
$current_user = wp_get_current_user();
$user_discount = (new Crm())->getUserDiscount($current_user->user_email);
$crm_user = (new Crm())->getCrmUser();

if($user_discount) {
    $levels = (new Crm())->getUserLevel();

    $user_discount_level = isset($levels[$user_discount]) ? $levels[$user_discount] : 0;

    if($user_discount_level):
        ?>

        <div class="person-info" style="clear: both; font-size: 10px; position: absolute; top: 5px; right:5px; font-family: sans-serif; letter-spacing: 0; ">
            <div style="float: left">
                <span class="user-name"><?=$current_user->display_name?></span>&nbsp;<!--<span class="user-level"><?/*=$user_discount_level*/?></span>--><br>
                <?php
                if($crm_user->card):
                    ?>
                    <span class="user-name">Номер карты: <?=sprintf("%013d", $crm_user->card);?></span>
                <?php
                endif;
                ?>
            </div>
            <img style="margin-left:0" id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>-mini.gif" title="Уровень: <?=$user_discount_level?>">

        </div>

    <?php
    endif;
}
?>