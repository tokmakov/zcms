<?php
/**
 * Для запуска из командной строки для формирования xml каталога
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

chdir('..');

// поддержка кодировки UTF-8
require 'app/include/utf8.php';
// автоматическая загрузка классов
require 'app/include/autoload.php';
// правила маршрутизации
require 'app/routing.php';
// настройки приложения
require 'app/settings.php';
Config::init($settings);
// реестр, для хранения всех объектов приложения
$register = Register::getInstance();
// сохраняем в реестре настройки, чтобы везде иметь к ним доступ; доступ к
// настройкам возможен через реестр или напрямую через Config::getInstance()
$register->config = Config::getInstance();
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();

if (is_file('catalog-temp.xml')) unlink('catalog-temp.xml');

file_put_contents('catalog-temp.xml', '<?xml version="1.0" encoding="utf-8" ?><catalog>', FILE_APPEND);

// получаем все категории
file_put_contents('catalog-temp.xml', '<categories>', FILE_APPEND);
$query = "SELECT `id`, `parent`, `name`, `sortorder` FROM `categories` WHERE 1 ORDER BY `id`";
$categories = $register->database->fetchAll($query);
foreach ($categories as $item) {
    echo 'category id='.$item['id'].PHP_EOL;
    $text = '<category id="' . $item['id'] . '" parent="' . $item['parent'] . '" sortorder="' . $item['sortorder'] . '">';
    $text = $text . '<![CDATA[' . trim($item['name']) . ']]>';
    $text = $text . '</category>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</categories>', FILE_APPEND);

// получаем всех производителей
file_put_contents('catalog-temp.xml', '<makers>', FILE_APPEND);
$query = "SELECT `id`, `name` FROM `makers` WHERE 1 ORDER BY `id`";
$makers = $register->database->fetchAll($query);
foreach ($makers as $item) {
    echo 'maker id='.$item['id'].PHP_EOL;
    $text = '<maker id="' . $item['id'] . '">';
    $text = $text . '<![CDATA[' . trim($item['name']) . ']]>';
    $text = $text . '</maker>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</makers>', FILE_APPEND);

// получаем функциональные группы
file_put_contents('catalog-temp.xml', '<groups>', FILE_APPEND);
$query = "SELECT `id`, `name` FROM `groups` WHERE 1 ORDER BY `id`";
$groups = $register->database->fetchAll($query);
foreach ($groups as $item) {
    echo 'group id='.$item['id'].PHP_EOL;
    $text = '<group id="' . $item['id'] . '">';
    $text = $text . '<![CDATA[' . trim($item['name']) . ']]>';
    $text = $text . '</group>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</groups>', FILE_APPEND);

// получаем все параметры и все значения
file_put_contents('catalog-temp.xml', '<params>', FILE_APPEND);
// получаем параметры
file_put_contents('catalog-temp.xml', '<names>', FILE_APPEND);
$query = "SELECT `id`, `name` FROM `params` WHERE 1 ORDER BY `id`";
$names = $register->database->fetchAll($query);
foreach ($names as $item) {
    echo 'param name id='.$item['id'].PHP_EOL;
    $text = '<name id="' . $item['id'] . '">';
    $text = $text . '<![CDATA[' . trim($item['name']) . ']]>';
    $text = $text . '</name>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</names>', FILE_APPEND);
// получаем значения
file_put_contents('catalog-temp.xml', '<values>', FILE_APPEND);
$query = "SELECT `id`, `name` FROM `values` WHERE 1 ORDER BY `id`";
$values = $register->database->fetchAll($query);
foreach ($values as $item) {
    echo 'param value id='.$item['id'].PHP_EOL;
    $text = '<value id="' . $item['id'] . '">';
    $text = $text . '<![CDATA[' . trim($item['name']) . ']]>';
    $text = $text . '</value>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</values>', FILE_APPEND);
file_put_contents('catalog-temp.xml', '</params>', FILE_APPEND);

// получаем все товары
file_put_contents('catalog-temp.xml', '<products>', FILE_APPEND);
$query = "SELECT * FROM `products` WHERE 1 ORDER BY `id` LIMIT 1000";
$products = $register->database->fetchAll($query);
foreach ($products as $item) {
    echo 'product id='.$item['id'].PHP_EOL;
    $category = $item['category'];
    if (!empty($item['category2'])) $category = $category.','.$item['category2'];
    $text = '<product code="' . $item['code'] . '" category="' . $category . '" group="' . $item['group'] . '" maker="' . $item['maker'] . '"  hit="' . $item['hit'] . '" new="' . $item['new'] . '" sortorder="' . $item['sortorder'] . '">';
    $text = $text . '<name><![CDATA[' . trim($item['name']) . ']]></name>';
    if (!empty($item['title'])) {
        $text = $text . '<title><![CDATA[' . trim($item['title']) . ']]></title>';
    } else {
        $text = $text . '<title/>';
    }
    if (!empty($item['shortdescr'])) {
        $text = $text . '<shortdescr><![CDATA[' . trim($item['shortdescr']) . ']]></shortdescr>';
    } else {
        $text = $text . '<shortdescr/>';
    }
    if (!empty($item['purpose'])) {
        $text = $text . '<purpose><![CDATA[' . trim($item['purpose']) . ']]></purpose>';
    } else {
        $text = $text . '<purpose/>';
    }
    $techdata = array();
    if (!empty($item['techdata'])) {
        $techdata = unserialize($item['techdata']);
    }
    $text = $text . '<techdata>';
    foreach ($techdata as $data) {
        $text = $text . '<item>';
        $text = $text . '<name><![CDATA[' . trim($data[0]) . ']]></name>';
        $text = $text . '<value><![CDATA[' . trim($data[1]) . ']]></value>';
        $text = $text . '</item>';
    }
    $text = $text . '</techdata>';
    if (!empty($item['features'])) {
        $text = $text . '<features><![CDATA[' . trim($item['features']) . ']]></features>';
    } else {
        $text = $text . '<features/>';
    }
    if (!empty($item['complect'])) {
        $text = $text . '<complect><![CDATA[' . trim($item['complect']) . ']]></complect>';
    } else {
        $text = $text . '<complect/>';
    }
    if (!empty($item['equipment'])) {
        $text = $text . '<equipment><![CDATA[' . trim($item['equipment']) . ']]></equipment>';
    } else {
        $text = $text . '<equipment/>';
    }
    if (!empty($item['additional'])) {
        $text = $text . '<additional><![CDATA[' . trim($item['equipment']) . ']]></additional>';
    } else {
        $text = $text . '<additional/>';
    }
    $text = $text . '<image>' . trim($item['image']) . '</image>';
    // файлы документации
    $text = $text . '<docs>';
    $query = "SELECT `doc_id` FROM `doc_prd` WHERE `prd_id` = :id";
    $docs = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($docs as $doc) {
        $text = $text . '<doc id="' . $doc['doc_id'] . '" />';
    }
    $text = $text . '</docs>';
    // сертификаты
    $text = $text . '<certs>';
    //$query = "SELECT `cert_id` FROM `cert_prod` WHERE `prod_id` = :id";
    //$docs = $register->database->fetchAll($query, array('id' => $item['id']));
    //foreach ($certs as $cert) {
        //$text = $text . '<cert id="' . $cert['cert_id'] . '">';
    //}
    $text = $text . '</certs>';
    // связанные товары
    $text = $text . '<linked>';
    $query = "SELECT `id2`, `sortorder` FROM `related` WHERE `id1` = :id ORDER BY `sortorder`";
    $prds = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($prds as $prd) {
        $code = $prd['id2'];
        if (strlen($code) == 4) $code = '00'.$code;
        if (strlen($code) == 5) $code = '0'.$code;
        $count = 11 - $prd['sortorder'];
        $text = $text . '<prd code="' . $code . '" count="' . $count . '" />';
    }
    $text = $text . '</linked>';

    $text = $text . '</product>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</products>', FILE_APPEND);

// получаем все файлы документации
file_put_contents('catalog-temp.xml', '<docs>', FILE_APPEND);
$query = "SELECT `id`, `title`, `filename`, `md5` FROM `docs` WHERE 1 ORDER BY `id` LIMIT 1000";
$docs = $register->database->fetchAll($query);
foreach ($docs as $item) {
    echo 'doc id='.$item['id'].PHP_EOL;
    $text = '<doc id="' . $item['id'] . '">';
    $text = $text . '<title><![CDATA[' . trim($item['title']) . ']]></title>';
    $text = $text . '<file>' . trim($item['filename']) . '</file>';
    $text = $text . '<md5>' . trim($item['md5']) . '</md5>';
    $text = $text . '</doc>';
    file_put_contents(
        'catalog-temp.xml',
        $text,
        FILE_APPEND
    );
}
file_put_contents('catalog-temp.xml', '</docs>', FILE_APPEND);

file_put_contents('catalog-temp.xml', '<certs></certs>', FILE_APPEND);

file_put_contents('catalog-temp.xml', '</catalog>', FILE_APPEND);
