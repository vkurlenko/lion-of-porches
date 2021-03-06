<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div>

    <fieldset>
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <label for="phone">Телефон&nbsp;<!--<span class="required">*</span>--></label>
            <input type="tel" class="woocommerce-Input woocommerce-Input--tel input-text" name="phone" id="phone" autocomplete="off" value="<?php echo esc_attr( $user->phone ); ?>" />
        </p>
        <div class="clear"></div>
    </fieldset>

	<fieldset>
		<legend><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<!--<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">-->
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<!--<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">-->
        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>


    <fieldset>
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="born_date">Дата рождения <span class="required">*</span></label>
            <input type="date" class="woocommerce-Input woocommerce-Input--password input-text" required name="born_date" id="born_date" autocomplete="off" value="<?php echo esc_attr( $user->born_date ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <label for="born_date">Пол</label>
            <select name="gender" class="woocommerce-Input" id="gender" >
                <option value="0" <?php echo !$user->gender ? 'selected' : '' ?>>Женский</option>
                <option value="1" <?php echo $user->gender ? 'selected' : '' ?>>Мужской</option>
            </select>
        </p>
    </fieldset>

    <fieldset>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_display_name">Ваш номер карты лояльности: <?= (new Crm())->getUserCard() ?></label>
            <input type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="user_card" id="user_card" value="<?= (new Crm())->getUserCard() ?>" /> <span></span>
        </p>
    </fieldset>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="subscribe" style="display: inline-block"><input type="checkbox" class="input-checkbox" name="subscribe" id="subscribe" <?=(new Crm())->getSubscribeStatus('subscribe') ? 'checked' : ''?> />Согласен на email рассылку</label>

        <br>
        <label for="sms" style="display: inline-block"><input type="checkbox" class="input-checkbox" name="sms" id="sms" <?=(new Crm())->getSubscribeStatus('sms') ? 'checked' : ''?> />Согласен на SMS рассылку</label>

        <label for="agreement" style="display: inline-block"><input type="checkbox" class="input-checkbox" name="agreement" id="agreement" required /> Настоящим я даю свое согласие ООО "Дом Луи" на обработку персональных данных и подтверждаю принятие условий <a href="/offerta/" target="_blank">Публичной оферты</a></label>

    </p>



    <div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
