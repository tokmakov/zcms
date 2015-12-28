<?php
/**
 * Для запуска из командной строки для заполнения таблицы temp_categories
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ZCMS', true);
chdir('../..');
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

$register->database->execute('TRUNCATE TABLE `temp_categories`');

require 'files/catalog/categories.php';
foreach ($categories as $category) {
    $data = array();
    $data['id'] = (int)$category['id'];
    echo $data['id'] . PHP_EOL;
    $data['parent'] = (int)$category['parent'];
    if ($data['parent'] == 2) {
        $data['parent'] = 0;
    }
    $data['name'] = trim($category['name']);
    $data['sortorder'] = (int)$category['sortorder'];
    $data['code'] = trim($category['code']);
    $query = "INSERT IGNORE INTO `temp_categories`
              (
                  `id`,
                  `parent`,
                  `name`,
                  `keywords`,
                  `description`,
                  `sortorder`,
                  `globalsort`,
                  `code`
              )
              VALUES
              (
                  :id,
                  :parent,
                  :name,
                  '',
                  '',
                  :sortorder,
                  '00000000000000000000',
                  :code
              )";
    $register->database->execute($query, $data);
}

// устанавливаем порядок сортировки категорий
$query ="SELECT `id` FROM `temp_categories` WHERE `parent` = 0 ORDER BY `sortorder`";
$roots = $register->database->fetchAll($query);
$i = 1;
foreach($roots as $root) {
    $sort = $i;
    if (strlen($sort) == 1) $sort = '0' . $sort;
    $query = "UPDATE
                  `temp_categories`
              SET
                  `sortorder` = ".$i.",
                  `globalsort` = '" . $sort . "000000000000000000'
              WHERE
                  `id` = " . $root['id'];
    // echo $query . PHP_EOL;
    $register->database->execute($query);
    updateSortOrderAllCategories($root['id'], $sort . '000000000000000000', 1);
    $i++;
}

$register->database->execute('TRUNCATE TABLE `categories`');

$query ="SELECT * FROM `temp_categories` WHERE 1";
$categories = $register->database->fetchAll($query);
foreach ($categories as $category) {
    $data = array();
    $data['id'] = (int)$category['id'];
    $data['parent'] = (int)$category['parent'];
    $data['name'] = trim($category['name']);
    $data['sortorder'] = (int)$category['sortorder'];
    $data['globalsort'] = trim($category['globalsort']);
    $query = "INSERT INTO `categories`
              (
                  `id`,
                  `parent`,
                  `name`,
                  `keywords`,
                  `description`,
                  `sortorder`,
                  `globalsort`
              )
              VALUES
              (
                  :id,
                  :parent,
                  :name,
                  '',
                  '',
                  :sortorder,
                  :globalsort
              )";
    $register->database->execute($query, $data);   
}



function updateSortOrderAllCategories($id, $sortorder, $level) {
    $register = Register::getInstance();
    // начало и конец строки, задающей сортировку
    $before = substr($sortorder, 0, $level * 2);
    $after = str_repeat('0', 18 - $level * 2);
    // получаем массив дочерних категорий
    $query = "SELECT `id` FROM `temp_categories` WHERE `parent` = ".$id." ORDER BY `sortorder`";
    $childs = $register->database->fetchAll($query);
    $i = 1;
    foreach($childs as $child) {
        $globalsort = $i;
        if (strlen($globalsort) == 1) {
            $globalsort = '0' . $globalsort;
        }
        $globalsort = $before . $globalsort . $after;
        $query = "UPDATE
                      `temp_categories`
                  SET
                      `sortorder` = ".$i.",
                      `globalsort` = '".$globalsort."'
                  WHERE
                      `id` = ".$child['id'];
        // echo $query . PHP_EOL;
        $register->database->execute($query);
        // рекурсивно вызываем updateSortOrderAllCategories()
        updateSortOrderAllCategories($child['id'], $globalsort, $level + 1);
        $i++;
    }
}
