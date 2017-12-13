<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
$database = array(               // соединение с базой данных
    'pcon'      => false,        // постоянное соединение?
    'host'      => 'localhost',
    'user'      => 'root',
    'pass'      => '',
    'name'      => 'zcms',
    /*
     * включена балансировка нагрузки между master и slave?
     * http://devacademy.ru/posts/prostaya-balansirovka-nagruzki-dlya-mysql-i-php-s-pomoschyu-biblioteki-mysqlnd/
     * http://phpprofi.ru/blogs/post/18, http://phpprofi.ru/blogs/post/22, http://phpprofi.ru/blogs/post/23
     */
    'balancing' => false,
);
