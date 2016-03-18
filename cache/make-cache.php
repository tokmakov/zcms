<?php
/**
 * Для запуска из командной строки для генерации кэша
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
$config = Config::getInstance();
/*
 * отмечаем, что приложение запущено из командной строки с целью формирования кэша
 */
$config->cache->make = true;
// реестр
$register = Register::getInstance();
// кэширование данных
$cache = Cache::getInstance();
// база данных
$database = Database::getInstance();

// очищаем кэш
// $cache->clearCache();
/*
// все страницы сайта
$query = "SELECT `id` FROM `pages` WHERE 1 ORDER BY `id`";
$pages = $database->fetchAll($query);
foreach($pages as $page) {
    // экземпляр класса роутера
    $router = Router::getInstance('Index_Page_Frontend_Controller', array('id' => $page['id']));
    // получаем имя класса контроллера
    $controller = $router->getControllerClassName();
    // параметры, передаваемые контроллеру
    $params = $router->getParams();
    // создаем экземпляр класса контроллера
    $p = new $controller($params);
    // формируем страницу
    $p->request();

    file_put_contents('cache/cache.txt', 'page-' . $page['id'] . PHP_EOL, FILE_APPEND);
    echo 'page-' . $page['id'] . PHP_EOL;

    $router->destroy();
    unset($register->indexPageFrontendController);
}

// все товары каталога
$query = "SELECT `id` FROM `products` WHERE `visible` = 1 ORDER BY `id` LIMIT 500";
$products = $database->fetchAll($query, array());
foreach($products as $product) {
    // экземпляр класса роутера
    $router = Router::getInstance('Product_Catalog_Frontend_Controller', array('id' => $product['id']));
    // получаем имя класса контроллера
    $controller = $router->getControllerClassName();
    // параметры, передаваемые контроллеру
    $params = $router->getParams();
    // создаем экземпляр класса контроллера
    $page = new $controller($params);
    // формируем страницу
    $page->request();

    file_put_contents('cache/cache.txt', 'product-' . $product['id'] . PHP_EOL, FILE_APPEND);
    echo 'product-' . $product['id'] . PHP_EOL;

    $router->destroy();
    unset($register->productCatalogFrontendController);
}

// все категории каталога
$query = "SELECT `id` FROM `categories` WHERE 1 ORDER BY `id` LIMIT 500";
$categories = $database->fetchAll($query, array());
foreach($categories as $category) {
    // экземпляр класса роутера
    $router = Router::getInstance('Category_Catalog_Frontend_Controller', array('id' => $category['id']));
    // получаем имя класса контроллера
    $controller = $router->getControllerClassName();
    // параметры, передаваемые контроллеру
    $params = $router->getParams();
    // создаем экземпляр класса контроллера
    $page = new $controller($params);
    // формируем страницу
    $page->request();

    file_put_contents('cache/cache.txt', 'category-' . $category['id'] . PHP_EOL, FILE_APPEND);
    echo 'category-' . $category['id'] . PHP_EOL;

    $router->destroy();
    unset($register->categoryCatalogFrontendController);
}

// все производители каталога
$query = "SELECT `id` FROM `makers` WHERE 1 ORDER BY `id` LIMIT 500";
$makers = $database->fetchAll($query);
foreach($makers as $maker) {
    // экземпляр класса роутера
    $router = Router::getInstance('Maker_Catalog_Frontend_Controller', array('id' => $maker['id']));
    // получаем имя класса контроллера
    $controller = $router->getControllerClassName();
    // параметры, передаваемые контроллеру
    $params = $router->getParams();
    // создаем экземпляр класса контроллера
    $page = new $controller($params);
    // формируем страницу
    $page->request();

    file_put_contents('cache/cache.txt', 'maker-' . $maker['id'] . PHP_EOL, FILE_APPEND);
    echo 'maker-' . $maker['id'] . PHP_EOL;

    $router->destroy();
    unset($register->makerCatalogFrontendController);
}
*/
// поиск по каталогу
$router = Router::getInstance('Index_Index_Frontend_Controller');
$searchCatalogFrontendModel
    = isset($register->searchCatalogFrontendModel) ? $register->searchCatalogFrontendModel : new Search_Catalog_Frontend_Model();
/*
$query = "SELECT LEFT(`name`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
*/
$query = "SELECT LEFT(`name`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 6) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}

$query = "SELECT LEFT(`code`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT `code` AS `search` FROM `products` WHERE 1 ORDER BY `code`";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
    echo 'search-' . md5($query['search']) . PHP_EOL;
}
