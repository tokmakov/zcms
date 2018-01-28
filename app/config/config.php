<?php
/**
 * Настройки приложения: логин-пароль для соединения с сервером БД,
 * логин и пароль администратора сайта, настройки кэширования, правила
 * маршрутизации, настройки CDN, мета-теги, подключаемые js и css-файлы,
 * постраничный вывод товаров, постов блога и т.п.
 */
defined('ZCMS') or die('Access denied');

// соединение с базой данных
require 'app/config/database.php';
// правила маршрутизации
require 'app/config/routing.php';
// настройки кэширования
require 'app/config/cache.php';
// настройки Content Delivery Network
require 'app/config/cdn.php';
// содержимое title, мета-теги keywords и description
require 'app/config/meta.php';
// CSS файлы, подключаемые к странице
require 'app/config/css.php';
// JavaScript файлы, подключаемые к странице
require 'app/config/js.php';
// постраничная навигация
require 'app/config/pager.php';
// защита от чрезмерно активных пользователей
require 'app/config/protector.php';

$config = array(
    'site' => array(
        'name'   => 'Торговый Дом ТИНКО',
        'phone'  => '+7 (495) 708-42-13',
        'email'  => 'tinko@tinko.info',
        'url'    => '//www.host2.ru/', /* //server.com/ или http://server.com/ или https://server.com/ */
        'theme'  => 'view/tinko', // путь к папке с темой
    ),
    'admin' => array( // логин-пароль администратора сайта
        'name'     => 'admin',
        'password' => 'qwerty',
    ),
    'email' => array(
        'admin' => 'tokmakov.e@mail.ru',         // e-mail администратора сайта
        'order' => 'tokmakov-e@yandex.ru',       // на этот адрес будут приходить письма о заказах
        'site'  => 'tinko@tinko.info',           // с этого адреса будут отправляться все письма
    ),
    'error' => array(
        'debug'    => true,                      // должен быть true на этапе разработки
        'write'    => true,                      // записывать сообщения об ошибках в журнал?
        'file'     => 'error.log.txt',           // файл журнала ошибок
        'sendmail' => false,                     // отправлять сообщения об ошибках на почту администратору?
    ),
    'message' => array( // информационные сообщения для пользователей
        // общее сообщение об ошибке, которое должно отображаться
        // вместо подробной информации (если debug равно false)
        'error'    => 'Произошла ошибка, сообщение об ошибке отправлено администратору.',
        // сообщение об успешном размещении заказа
        'checkout' => 'Заявка на оборудование создана, наш менеджер свяжется с Вами в ближайшем будущем.',
    ),
    'user'  => array(
        'prefix' => '',  // префикс к паролю пользователя для усложнения взлома
        'cookie' => 365, // время хранения уникального идентификатора посетителя в днях
    ),

    'database'  => $database,                    // см. файл app/config/database.php
    'sef'       => $routing,                     // см. файл app/config/routing.php
    'cache'     => $cache,                       // см. файл app/config/cache.php
    'cdn'       => $cdn,                         // см. файл app/config/cdn.php
    'meta'      => $meta,                        // см. файл app/config/meta.php
    'css'       => $css,                         // см. файл app/config/css.php
    'js'        => $js,                          // см. файл app/config/js.php
    'pager'     => $pager,                       // см. файл app/config/pager.php
    'protector' => $protector,                   // см. файл app/config/protector.php

);

unset(
    $database,
    $routing,
    $cache,
    $cdn,
    $meta,
    $css,
    $js,
    $pager,
    $protector
);
