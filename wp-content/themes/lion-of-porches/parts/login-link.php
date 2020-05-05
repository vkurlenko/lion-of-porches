<?php if ( is_user_logged_in() ) { ?>
    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Личный кабинет','evolution'); ?>"><?php _e('Личный кабинет','woothemes'); ?></a>
<?php } else { ?>
    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Вход/Регистрация','evolution'); ?>"><?php _e('Войти','woothemes'); ?></a>
<?php } ?>