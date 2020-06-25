<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.05.2020
 * Time: 21:43
 */

class Crm
{
    /**
     * Создание нового пользователя из CRM
     *
     * @param $POST
     */
    public function createUser($POST)
    {
        //$user_id = register_new_user( $POST['login'], $POST['email'] );
        $random_password = wp_generate_password( 12 );

        //$user_id = wp_create_user( $POST['login'], $random_password, $POST['login'] );

        $userdata = array(
            //'ID'              => 0,  // когда нужно обновить пользователя
            'user_pass'       => wp_hash_password($random_password), // обязательно
            'user_login'      => $POST['login'], // обязательно
            'user_nicename'   => $POST['login'],
            'user_url'        => '',
            'user_email'      => $POST['login'],
            'display_name'    => $POST['user_name'],
            'nickname'        => '',
            'first_name'      => '',
            'last_name'       => $POST['user_name'],
            'description'     => '',
            'rich_editing'    => 'false', // false - выключить визуальный редактор
            'user_registered' => '', // дата регистрации (Y-m-d H:i:s) в GMT
            'role'            => 'customer', // (строка) роль пользователя
            'jabber'          => '',
            'aim'             => '',
            'yim'             => ''
        );

        $user_id = wp_insert_user( $userdata );

        //$this->sendRegisterInfo($POST['login'], $POST['login'], $random_password);

        /*$WC_API_Customers = new WC_API_Customers();
        $customer = $WC_API_Customers->get_customer( $user_id );
        $customer->set_billing_phone( $POST['phone'] );*/

        wp_new_user_notification( $user_id, null, 'both' );
    }

    /*public function sendRegisterInfo($to, $login, $password)
    {
        $subject = sprintf('Регистрационные данные на сайте lion-of-porches.ru');

        $message = sprintf('Ваш логин: %s \r\nПароль: %s', $login, $password);

        wp_mail( $to, $subject, $message);
    }*/

    /**
     *  Импорт учетных записей клиентов из CRM
     */
    public function importUsers()
    {
        global $wpdb;

        $sql = 'SELECT * FROM `data3` limit 0, 2000';
        $res = $wpdb->get_results($sql);

        $errors = 0;
        $i = 0;

        foreach($res as $row) {
            echo $i++.'   ';

            $row = (array)$row;

            if(trim($row['email']) == '') {
                if(trim($row['phone']) != '') {
                    $row['login'] = $row['phone'];
                } else {
                    echo $row['user_name'].' Email и телефон не указаны<br>';
                    $errors++;
                    continue;
                }
            } else {
                $is_exists = $this->findUserByEmail($row['email']);

                if($is_exists) {
                    echo $row['user_name'].' уже существует<br>';
                    $errors++;
                    continue;
                }

                $row['login'] = $row['email'];
            }

            echo 'Создадим '.$row['user_name'].' '.$row['login'].'<br>';

            $this->createUser($row);

        }

        echo 'Не импортировано '.$errors.' записей';

        die;
    }

    /**
     * Поиск уже зарегистированного пользователя по email
     *
     * @param $email
     * @return bool
     */
    public function findUserByEmail($email)
    {
        global $wpdb;

        $is_exists = false;

        $sql = 'SELECT * FROM `wp_users` WHERE user_email="'.$email.'"';

        $isset = $wpdb->get_results($sql);

        if(!empty($isset)) {
            $is_exists = true;
        }

        return $is_exists;
    }

    /**
     * Получить размер скидки из CRM по email клиента
     *
     * @param $user_email
     * @return bool
     */
    public function getUserDiscount($user_email)
    {
        if(!$user_email) {
            return false;
        }

        global $wpdb;

        $sql = 'SELECT * FROM `data3` WHERE email="'.$user_email.'" limit 1';

        //echo $sql;

        $res = $wpdb->get_results($sql);

        //var_dump($res); //die;

        if(!empty($res[0])) {
            if($res[0]->discount) {
                return $res[0]->discount;
            }
        }
        return false;
    }

    /**
     * Получим данные текущего авторизованного клиента из CRM
     *
     * @return array
     */
    public function getCrmUser()
    {
        /*
    [id] => 576
    [code] => 0
    [name] => 0
    [card] => 52936
    [activation_date] => 2019-11-06
    [discount] => 30
    [user_name] => Виктор
    [gender] => 1
    [age] => 0
    [born_year] => 0
    [born_date] => 2019-11-01
    [email] => vkurlenko@yandex.ru
    [phone] => 234234
    [comment] => qwe
    [subscribe] => 1
    [sms] => 0
    [send_status] => 0
        */

        global $wpdb;

        if ( is_user_logged_in() ) {

            $current_user = wp_get_current_user();

            if($current_user->user_email) {
                $sql = 'SELECT * FROM `data3` WHERE email="'.$current_user->user_email.'" limit 1';
                $res = $wpdb->get_results($sql);

                if(!empty($res[0])) {
                    return $res[0];
                }
            }
        }

        return [];
    }

    /**
     * Персональная скидка текущего пользователя (%)
     *
     * @return bool|int
     */
    public function getCurrentUserDiscount()
    {
        if ( is_user_logged_in() ) {

            $current_user = wp_get_current_user();

            $discount = $this->getUserDiscount($current_user->user_email);

            //echo $discount; die;

            if($discount) {
                return $discount;
            }
        }

        return 0;
    }

    /**
     * Получить размер персональной скидки для товаров распродажи (%)
     *
     * @return array
     */
    public function getUserSaleDiscount($sale_discount = 0, $user_discount = 0)
    {
        if(!$sale_discount || !$user_discount || !is_user_logged_in()) {
            return 0;
        }

        switch($sale_discount) {
            case $sale_discount >= 30:
                $sale_discount = 30;
                break;

            case $sale_discount >= 20:
                $sale_discount = 20;
                break;

            case $sale_discount >= 10:
                $sale_discount = 10;
                break;

            default:
                $sale_discount = 0;
        }

        //echo $sale_discount;

        $arr = [
            10 => [
                '5' => 0,
                '10' => 1,
                '15' => 7,
                '20' => 13,
                '25' => 19,
                '30' => 25,
                '50' => 25
            ],
            20 => [
                '5' => 0,
                '10' => 2,
                '15' => 3,
                '20' => 4,
                '25' => 10,
                '30' => 18,
                '50' => 18
            ],
            30 => [
                '5' => 2,
                '10' => 3,
                '15' => 4,
                '20' => 6,
                '25' => 7,
                '30' => 8,
                '50' => 8
            ]
        ];

        if($sale_discount && $user_discount) {
            return isset($arr[$sale_discount][$user_discount]) ? $arr[$sale_discount][$user_discount] : 0;
        }

        return 0;
    }

    /**
     * Получим статус клиента в зависимости от размера скидки
     *
     * @return array
     */
    public function getUserLevel()
    {
        $arr = [
            '5' => 'Basic',
            '10' => 'Silver',
            '15' => 'Gold',
            '20' => 'Platinum',
            '25' => 'Signiture',
            '30' => 'Infinity',
            '50' => 'Infinity'
        ];

        return $arr;
    }

    /**
     * Сумма покупок для каждого уровня клиента
     *
     * @return array
     */
    public function getLevelLimits()
    {
        $arr = [
            '5' => 10000,
            '10' => 70000,
            '15' => 15000,
            '20' => 250000,
            '25' => 350000,
            '30' => 500000,
            '50' => 500000
        ];

        return $arr;
    }

    public function getNextLevelUpSum($current_sum)
    {
        $next_sum = 0;

        foreach($this->getLevelLimits() as $level => $sum) {
            if($current_sum < $sum) {
                $next_sum = $sum;

                return $next_sum;
            }
        }

        return $next_sum;
    }

    public function getLevelBySum($current_sum = 0)
    {
        $levels = $this->getLevelLimits();

        foreach($levels as $level => $sum) {
            if($current_sum == $sum) {
                return $level;
            }
        }

        return 0;
    }

    public function getSubscribeStatus($type = 'subscribe')
    {
        global $wpdb;

        $crm_user = $this->getCrmUser();

        if($crm_user) {
            $sql = "SELECT * FROM `data3` WHERE id = ".$crm_user->id;

            $res = $wpdb->get_results($sql);

            switch($type) {
                case 'sms': $field = $res[0]->sms;
                    break;

                default: $field = $res[0]->subscribe;
                    break;
            }

            return $field;
        }

        return false;

    }

    public function setSubscribeStatus($type = 'subscribe', $status = null)
    {
        global $wpdb;

        if($status !== null) {
            $crm_user = $this->getCrmUser();

            if($crm_user) {

                switch($type) {
                    case 'sms': $field = 'sms';
                    break;

                    default: $field = 'subscribe';
                        break;
                }

                $sql = "UPDATE `data3` SET ".$field."='".(int)$status."' WHERE id = ".$crm_user->id;

                $wpdb->query($sql);
            }

            //(new Helper())->dump($crm_user); die;
        }
    }

/*Limits	Discription	Discounts
10,000 ₽	Basic	5%
70,000 ₽	Silver	10%
150,000 ₽	Gold	15%
250,000 ₽	Platinum	20%
350,000 ₽	Signiture	25%
500,000 ₽	Ultra	30%*/
}