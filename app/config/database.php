<?php
defined('ZCMS') or die('Access denied');

$database = array(               // соединение с базой данных
    'pcon'      => false,        // постоянное соединение?
    'host'      => 'localhost',
    'user'      => 'root',
    'pass'      => 'wbmstr',
    'name'      => 'zcms2',
    'balancing' => false,        // включена балансировка нагрузки между master и slave?
);