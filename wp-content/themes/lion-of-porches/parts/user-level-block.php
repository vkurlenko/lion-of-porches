<?php
$current_user = wp_get_current_user();
$user_discount = (new Crm())->getUserDiscount($current_user->user_email);
$crm_user = (new Crm())->getCrmUser();

if($user_discount) {
    $levels = (new Crm())->getUserLevel();

    $user_discount_level = isset($levels[$user_discount]) ? $levels[$user_discount] : 0;
    /*echo 'level='.$user_discount_level;
    (new Helper())->dump($current_user);*/
    if($user_discount_level):
    ?>

    <div class="user-discount-level">
        <span class="user-name"><?=$current_user->display_name?></span>
        <?php
        if($crm_user->card):
        ?>
            <span class="user-name">Номер карты: <strong><?=sprintf("%013d", $crm_user->card);?></strong></span>
        <?php
        endif;
        ?>
        <span class="user-level"><?=$user_discount_level?></span>
        <img id="user-level-label" src="/wp-content/themes/lion-of-porches/img/levels/<?=strtolower($user_discount_level)?>.jpg">
    </div>
    <?php
    endif;
}
?>