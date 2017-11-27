<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
$protector = array( // защита от чрезмерно активных пользователей
    'enable' => true, // включить защиту?
    'white'  => array( // эти ip-адреса не блокируем
                    '127.0.0.1',
                ),
    'time'   => 60,    // блокировать на … секунд, если
    'stack'  => 20,    // в течение … секунд
    'hits'   => 20     // сделано более … хитов
);