<?php
/**
 * Абстрактный класс Base, родительский для всех контроллеров и моделей
 */
abstract class Base {

    /**
     * для доступа ко всем объектам приложения, экземпляр класса Register
     */
    protected $register;

    /**
     * для доступа к настройкам приложения, экземпляр класса Config
     */
    protected $config;

    /**
     * для хранения экземпляра класса Cache
     */
    protected $cache;

    /**
     * для хранения экземпляра класса базы данных Database
     */
    protected $database;

    /**
     * административная часть сайта?
     */
    protected $backend = false;


    public function __construct() {

        // все объекты приложения, экземпляр класса Register
        $this->register = Register::getInstance();
        // настройки приложения, экземпляр класса Config
        $this->config = Config::getInstance();
        // экземпляр класса Cache
        $this->cache = Cache::getInstance();
        // экземпляр класса базы данных
        $this->database = Database::getInstance();
        // административная часть сайта?
        $this->backend = Router::getInstance()->isBackend();
        // сохраняем в реестре экземпляр текущего класса
        $class = str_replace('_', '', lcfirst(get_class($this)));
        if (isset($this->register->$class)) {
            throw new Exception('Попытка создать второй экземпляр класса ' . get_class($this));
        }
        $this->register->$class = $this;

    }

    /**
     * Функция осуществляет редирект на переданный в качестве параметра URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        die();
    }

    /**
     * Функция возвращает true, если данные пришли методом POST
     */
    protected function isPostMethod() {
        return isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST');
    }

    /*
     * Четыре функции для обмена данными между страницами с помощью сессий.
     * Если требуется предать какие-то данные от одной страницы другой, то
     * первая вызывает setSessionData(), вторая вызывает getSessionData().
     */

    /**
     * Функция сохраняет данные в сессии
     */
    protected function setSessionData($key, $data) {
        $_SESSION['zcmsSessionData'][$key] = $data;
    }

    /**
     * Функция возвращает сохраненные в сессии данные
     */
    protected function getSessionData($key) {
        if (!isset($_SESSION['zcmsSessionData'][$key])) {
            throw new Exception('Данные сессии с ключом ['.$key.'] не найдены');
        }
        return $_SESSION['zcmsSessionData'][$key];
    }

    /**
     * Функция удаляет сохраненные в сессии данные
     */
    protected function unsetSessionData($key) {
        if (isset($_SESSION['zcmsSessionData'][$key])) {
            unset($_SESSION['zcmsSessionData'][$key]);
        }
    }

    /**
     * Функция проверяет существование сохраненных в сессии данных
     */
    protected function issetSessionData($key) {
        return isset($_SESSION['zcmsSessionData'][$key]);
    }

}