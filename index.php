<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

// обновление каталога
if (is_file('cron/update.txt')) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 600');
    readfile('cron/update.html');
    die();
}

session_start();

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

try {
    // экземпляр класса роутера
    $router = Router::getInstance();
    $register->router = $router;
    /*
     * Получаем имя класса контроллера, например Page_Frontend_Controller. Если
     * класс контроллера не найден, работает контроллер Notfound_Frontend_Controller
     * или Notfound_Backend_Controller
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
         * контроллеру, например Page_Frontend_Controller, были переданы некорректные
         * параметры. Пример: frontend/page/id/12345, но страницы с уникальным id=12345
         * нет в таблице pages базы данных. Это возможно, если страница (новость, товар)
         * была удалена или пользователь ошибся при вводе URL страницы.
         */
        $router->setNotFound();
        // работет контроллер Notfound_Frontend_Controller или Notfound_Backend_Controller
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
