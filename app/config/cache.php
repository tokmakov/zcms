<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
$cache = array(
    'enable' => array(
        'data' => false,                     // кэширование данных разрешено?
        'html' => false,                     // кэширование шаблонов разрешено?
    ),
    'file'   => array(                       // кэширование с использованием файлов
        'time' => 7200,                      // время храниения кэша в секундах
        'lock' => 10,                        // максимальное время блокировки на чтение в секундах
        'dir'  => 'cache',                   // директория для хранения файлов кэша
    ),
    'mem'    => array(                       // кэширование с использованием Memcached
        'time' => 3600,                      // время храниения кэша в секундах
        'lock' => 10,                        // максимальное время блокировки на чтение в секундах
        'host' => 'localhost',
        'port' => 11211,
    ),
);