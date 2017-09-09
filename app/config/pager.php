<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
$pager = array(                // постраничная навигация
    'frontend' => array(       // общедоступная часть сайта
        'article'   => array(
            'perpage'   => 6,  // статей на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'blog'      => array(
            'perpage'   => 10, // постов на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'products'  => array(
            'perpage'   => 10, // товаров на страницу (по умолчанию)
            'others'    => array(20, 50, 100), // другие варианты кол-ва товаров на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'orders'    => array(
            'perpage'   => 5,  // заказов на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'solution'  => array(
            'perpage'   => 5,  // типовых решений на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
    ),
    'backend' => array(        // административная часть сайта
        'article'  => array(
            'perpage'   => 20, // статей на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'blog'     => array(
            'perpage'   => 20, // постов на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'products' => array(
            'perpage'   => 20, // товаров на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'orders'   => array(
            'perpage'   => 20, // заказов на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
        'users'    => array(
            'perpage'   => 20, // пользователей на страницу
            'leftright' => 2,  // кол-во ссылок слева и справа
        ),
    )
);