<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.05.2020
 * Time: 21:43
 */

class Crm
{
    public function createUser($login = '', $pwd = '')
    {
        $user_id = register_new_user( 'vkurlenko', 'vkurlenko@ya.ru' );
    }
}