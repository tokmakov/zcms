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
// реестр, для хранения всех объектов приложения
$register = Register::getInstance();
// сохраняем в реестре настройки, чтобы везде иметь к ним доступ; доступ к
// настройкам возможен через реестр или напрямую через Config::getInstance()
$register->config = Config::getInstance();
/*
 * отмечаем, что приложение запущено из командной строки для формирования кэша
 */
$register->config->cache->make = true;
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();
/*
$query = "SELECT `id` FROM `pages` WHERE 1 ORDER BY `id`";
$pages = $register->database->fetchAll($query, array());
foreach($pages as $page) {
	// экземпляр класса роутера
	$router = Router::getInstance('Page_Frontend_Controller', array('id' => $page['id']));
	$register->router = $router;
	// получаем имя класса контроллера, например Page_Frontend_Controller
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
	unset($register->router);
	unset($register->pageFrontendController);
}
*/
$query = "SELECT `id` FROM `products` WHERE `visible` = 1 ORDER BY `id` LIMIT 10";
$products = $register->database->fetchAll($query, array());
foreach($products as $product) {
	// экземпляр класса роутера
	$router = Router::getInstance('Product_Catalog_Frontend_Controller', array('id' => $product['id']));
	$register->router = $router;
	// получаем имя класса контроллера, например Page_Frontend_Controller
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
	unset($register->router);
	unset($register->productCatalogFrontendController);
}
/*
$query = "SELECT `id` FROM `categories` WHERE 1 ORDER BY `id`";
$categories = $register->database->fetchAll($query, array());
foreach($categories as $category) {
	// экземпляр класса роутера
	$router = Router::getInstance('Category_Catalog_Frontend_Controller', array('id' => $category['id']));
	$register->router = $router;
	// получаем имя класса контроллера, например Page_Frontend_Controller
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
	unset($register->router);
	unset($register->categoryCatalogFrontendController);
}

$query = "SELECT `id` FROM `makers` WHERE 1 ORDER BY `id`";
$makers = $register->database->fetchAll($query, array());
foreach($makers as $maker) {
	// экземпляр класса роутера
	$router = Router::getInstance('Maker_Catalog_Frontend_Controller', array('id' => $maker['id']));
	$register->router = $router;
	// получаем имя класса контроллера, например Page_Frontend_Controller
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
	unset($register->router);
	unset($register->makerCatalogFrontendController);
}

$router = Router::getInstance('Index_Frontend_Controller');
$register->router = $router;
$catalogFrontendModel = isset($register->catalogFrontendModel) ? $register->catalogFrontendModel : new Catalog_Frontend_Model();

$query = "SELECT LEFT(`name`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`name`, 6) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}

$query = "SELECT LEFT(`code`, 2) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 3) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 4) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
$query = "SELECT LEFT(`code`, 5) AS `search`, COUNT(*) FROM `products` WHERE 1 GROUP BY 1 ORDER BY 2 DESC";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}

$query = "SELECT `code` AS `search` FROM `products` WHERE 1 ORDER BY `code`";
$queries = $register->database->fetchAll($query, array());
foreach($queries as $query) {
	$result = $catalogFrontendModel->getSearchResults($query['search'], 0, true);
	file_put_contents('cache/cache.txt', $query['search'] . PHP_EOL, FILE_APPEND);
	echo 'search-' . md5($query['search']) . PHP_EOL;
}
*/