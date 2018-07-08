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

 if (empty($_SERVER['SERVER_NAME'])){
   exit;
 } else {
   $server_name = $_SERVER['SERVER_NAME'];
 }

	if('dev.wordpressify.ru' == $server_name ){

	 // ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
	 /** Имя базы данных для WordPress */
	 define('DB_NAME', 'vagrant');

	 /** Имя пользователя MySQL */
	 define('DB_USER', 'vagrant');

	 /** Пароль к базе данных MySQL */
	 define('DB_PASSWORD', 'password');

	 /** Имя сервера MySQL */
	 define('DB_HOST', 'localhost');

	 /** Кодировка базы данных для создания таблиц. */
	 define('DB_CHARSET', 'utf8mb4');

	 /** Схема сопоставления. Не меняйте, если не уверены. */
	 define('DB_COLLATE', '');

	 /**#@+
	  * Уникальные ключи и соли для аутентификации.
	  *
	  * Смените значение каждой константы на уникальную фразу.
	  * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
	  * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
	  *
	  * @since 2.6.0
	  */
	 define('AUTH_KEY',         'JPq-#UGU1QJ/>pXGilH>==*%L;s*?i4*LL?q^IF}tjndO#Hh}u!JyPR4#2.5,W_e');
	 define('SECURE_AUTH_KEY',  'L.E1IxbhoX{%f);I]]M%IBBbagsZH&-7C}55-#l9lMbp@5A!RBd!S[Uiz@*lFa9v');
	 define('LOGGED_IN_KEY',    '>f<w+rDXYcanF>:tMY1a PS[M<!Vut-dG))z(DuDBYZAEU@6yYK92sZ.umtB{xML');
	 define('NONCE_KEY',        'GSBO0=4[&y8cO;Jqpo|Y/ZS6ugsCF]U[^}7EjKJv+H:NvDTa{vZ-1r&a-WY^rl?E');
	 define('AUTH_SALT',        '*s5MR`yh< &vPotvU<B_Y=/^47~L.#A8]jv9Gk1{%RiDnr(y{0x`a&/o`enk#~{w');
	 define('SECURE_AUTH_SALT', ']nc~M]kA:B661d TUhb1 =D%L^C2n%C<8=`f1Nz-`=]AL:vt<&Z^LS8_m$dfdPGg');
	 define('LOGGED_IN_SALT',   '@C4U,0(xVbxCtPd7MGV,?G*(gM.EP^My>62kV{FXEpB@GS)#-X+#)$`(~&y@-L6e');
	 define('NONCE_SALT',       'Wg0jwvbW:JyZzB46-1?QQmy,M9=F(wQ]M&>oGRt{rxso(w4%rif3Q!x_o$k`G{>j');

	 /**
	  * Префикс таблиц в базе данных WordPress.
	  *
	  * Можно установить несколько сайтов в одну базу данных, если использовать
	  * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
	  */
	 $table_prefix  = 'pc438_';

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
	 define('WP_DEBUG', true);
	}

	if('wordpressify.ru' == $server_name ){
	 include_once 'wp-config-live.php';
	}


/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
