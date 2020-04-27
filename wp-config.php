<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'lip' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/*
 * 'dsn' => 'mysql:host=vinci-2.mysql;dbname=vinci-2_lip',
    'username' => 'vinci-2_mysql',//'vinci-1_lip',
    'password' => 'sbanfy3e',//VNS4BXXK4XizphtOtiB7',
    'charset' => 'utf8',*/

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'l=CiW==165-m;k-|,||Z5R]/[ Wo~J)Z5v&]qm n-JBwy;?Wgsb|@V^wEYLbPKnF');
define('SECURE_AUTH_KEY',  'H|=Q-@oz{R<pt#cm ^Pl*h/c+tK9s}unB~9JHcR$`+6u-/LauN-.|GxAbYTVh5zz');
define('LOGGED_IN_KEY',    'tV1D~FKOh_a4Q2Uy0ZLSaH=e{ggUN*t+Q]-=d$bM%W*NAR$A#|8V}zYC2r@v->gT');
define('NONCE_KEY',        '=j iKxYkWO+UJzUB]Oi~%mhRVZdOBYy=gXmb65{/M+EXj@-^V@Hziks;P.|`;m+y');
define('AUTH_SALT',        'BKJ$2^6j.a 6,LkyWDx-y[-`wL=q0ato55D<dQa#:G>wI95.t_>F_J5YOYkRULtz');
define('SECURE_AUTH_SALT', 'pyefz&^ic.VwkfQHl&eUp?Aqs|8+=g,(-@<BT{Rw`,{&Lu_LFy.Tf37ITLnXPwAL');
define('LOGGED_IN_SALT',   '4Q*Cs6(R`4Y~JE|/^5^K: CU%n>&s!/kYDCKfM*~pw>0^~>xd#Q|GF&~(.&f:T#N');
define('NONCE_SALT',       'O@7>pfO)]4{w:3[|O6Pvs5HfvC4Q<78q0Qu1fS((mm-Ik:@c&WG%*U]C#BU@-<s{');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );
define ('WPCF7_LOAD_JS', false );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
