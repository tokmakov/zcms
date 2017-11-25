<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

// идет обновление каталога
if (is_file('cron/update.txt')) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 600');
    header('Content-Type: text/html; charset=utf-8');
    readfile('cron/update.html');
    die();
}

session_start();

// автоматическая загрузка классов
require 'app/include/autoload.php';
// настройки приложения
require 'app/config/config.php';
// инициализация настроек
Config::init($config);
unset($config);

// защита от чрезмерно активных пользователей
if (Config::getInstance()->protect->enable) {
    $protect = new Protect();
    unset($protect);
}

try {
    // экземпляр класса роутера
    $router = Router::getInstance();
    /*
     * Получаем имя класса контроллера, например Index_Page_Frontend_Controller. Если
     * класс контроллера не найден, работает контроллер Index_Notfound_Frontend_Controller
     * или Index_Notfound_Backend_Controller
     */
    $controller = $router->getControllerClassName();
    // параметры, передаваемые контроллеру
    $params = $router->getParams();
    // создаем экземпляр класса контроллера
    $page = new $controller($params);
    // формируем страницу
    $page->request();
    if ($page->isNotFoundRecord()) {
        /*
         * Функция Base_Controller::isNotFoundRecord() возвращает true, если какому-либо
         * контроллеру, например Index_Page_Frontend_Controller, были переданы некорректные
         * параметры. Пример: frontend/page/index/id/12345, но страницы с уникальным id=12345
         * нет в таблице pages базы данных. Это возможно, если страница (новость, товар)
         * была удалена или пользователь ошибся при вводе URL страницы. См. комментариии в
         * начале класса Base_Controller
         */
        $router->setNotFound();
        // работет контроллер Index_Notfound_Frontend_Controller
        // или Index_Notfound_Backend_Controller
        $controller = $router->getControllerClassName();
        $page = new $controller();
        $page->request();
    }
} catch (Exception $e) { // если произошла какая-то ошибка
    $page = new Error($e);
    die();
}
// отправляем заголовки
$page->sendHeaders();
// выводим сформированную страницу в браузер
echo $page->getPageContent();
