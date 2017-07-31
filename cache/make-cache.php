<?php
/**
 * Запуск из командной строки для генерации кэша
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

chdir('..');

// автоматическая загрузка классов
require 'app/include/autoload.php';
// настройки приложения
require 'app/config/config.php';
Config::init($config);
unset($config);

if (is_file('cache/cache.txt')) {
    unlink('cache/cache.txt');
}

/*
 * отмечаем, что приложение запущено из командной строки с целью формирования кэша
 */
Config::getInstance()->cache->make = true;

// реестр
$register = Register::getInstance();
// кэширование данных
$cache = Cache::getInstance();
// база данных
$database = Database::getInstance();

// очищаем кэш
$cache->clearCache();

/*
 * все страницы сайта
 */
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

    echo 'page-' . $page['id'] . PHP_EOL;

    $router->destroy();
    unset($register->indexPageFrontendController);

    usleep(100);
}

/*
 * все товары каталога
 */
$query = "SELECT `id` FROM `products` WHERE `visible` = 1 ORDER BY `id`";
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

    echo 'product-' . $product['id'] . PHP_EOL;

    $router->destroy();
    unset($register->productCatalogFrontendController);

    usleep(100);
}

/*
 * все категории каталога
 */
$query = "SELECT `id` FROM `categories` WHERE 1 ORDER BY `id`";
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

    echo 'category-' . $category['id'] . PHP_EOL;

    $router->destroy();
    unset($register->categoryCatalogFrontendController);

    usleep(100);
}

/*
 * все производители каталога
 */
$query = "SELECT `id` FROM `makers` WHERE 1 ORDER BY `id`";
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

    echo 'maker-' . $maker['id'] . PHP_EOL;

    $router->destroy();
    unset($register->makerCatalogFrontendController);

    usleep(100);
}

/*
 * поиск по каталогу
 */
$router = Router::getInstance('Index_Index_Frontend_Controller');
$searchCatalogFrontendModel
    = isset($register->searchCatalogFrontendModel) ? $register->searchCatalogFrontendModel : new Search_Catalog_Frontend_Model();

// торговое наименование, первые два символа
$query = "SELECT LEFT(`name`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// торговое наименование, первые три символа
$query = "SELECT LEFT(`name`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// торговое наименование, первые четыре символа
$query = "SELECT LEFT(`name`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// торговое наименование, первые пять символов
$query = "SELECT LEFT(`name`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// торговое наименование, первые шесть символов
$query = "SELECT LEFT(`name`, 6) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}

// код (артикул), первые два символа
$query = "SELECT LEFT(`code`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// код (артикул), первые три символа
$query = "SELECT LEFT(`code`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// код (артикул), первые четыре символа
$query = "SELECT LEFT(`code`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// код (артикул), первые пять символов
$query = "SELECT LEFT(`code`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
// код (артикул), все шесть символов
$query = "SELECT `code` AS `search` FROM `products` WHERE 1 ORDER BY `code`";
$queries = $database->fetchAll($query);
foreach($queries as $query) {
    // обращаемся к методу модели для поиска по каталогу
    $result = $searchCatalogFrontendModel->getSearchResults($query['search'], 0, true);
    echo 'search-' . md5($query['search']) . PHP_EOL;
    usleep(100);
}
