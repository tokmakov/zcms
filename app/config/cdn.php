<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
$cdn = array(                          // Content Delivery Network
    'enable' => array(
        'js'     => true,              // js-файлы
        'css'    => true,              // css-файлы
        'img'    => true,              // фото товаров
        'doc'    => true,              // файлы документации
        'cert'   => true,              // файлы сертификатов
        'blog'   => true,              // thumbnails постов блога
        'banner' => true,              // баннеры справа
        'slider' => true,              // слайдер на главной
    ),
    'url'    => '//cdn.host2.ru/',
);