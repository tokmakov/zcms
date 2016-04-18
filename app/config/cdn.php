<?php
defined('ZCMS') or die('Access denied');

$cdn = array(                           // Content Delivery Network
    'enable' => array(
        'js'     => false,              // js-файлы
        'css'    => false,              // css-файлы
        'img'    => false,              // фото товаров
        'doc'    => false,              // файлы документации
        'cert'   => false,              // файлы сертификатов
        'blog'   => false,              // thumbnails постов блога
        'banner' => false,              // баннеры справа
        'slider' => false,              // слайдер на главной
    ),
    'url'    => 'http://cdn.host2.ru/',
);