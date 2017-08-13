<?php
/**
 * Класс Router анализирует строку $_SERVER['REQUEST_URI'] и позволяет определить,
 * какой контроллер должен быть вызван, чтобы сформировать страницу сайта; реализует
 * шаблон проектирования «Одиночка»
 */
class Router {

    /**
     * для хранения единственного экземпляра данного класса
     */
    private static $instance;

    /**
     * запрос с использованием XmlHttpRequest?
     */
    private $xhr = false;

    /**
     * имя контроллера, по умолчанию index
     */
    private $controller = 'index';

    /**
     * имя действия (экшен), по умолчанию index
     */
    private $action = 'index';

    /**
     * полное имя класса контроллера
     */
    private $controllerClassName = 'Index_Index_Frontend_Controller';

    /**
     * массив параметров, которые будут переданы контроллеру
     */
    private $params = array();

    /**
     * идет работа с админкой?
     */
    private $backend = false;

    /**
     * настройки приложения, экземпляр класса Config
     */
    private $config;

    /**
     * для хранения всех объектов приложения, экземпляр класса Register
     */
    private $register;

    /**
     * для хранения экземпляра класса Cache
     */
    private $cache;

    /**
     * для хранения экземпляра класса базы данных Database
     */
    private $database;

    /**
     * кэширование данных разрешено?
     */
    protected $enableDataCache;


    /**
     * Функция возвращает ссылку на экземпляр данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    public static function getInstance($class = null, $params = array()){
        if (is_null(self::$instance)) {
            self::$instance = new self($class, $params);
        }
        return self::$instance;
    }

    /**
     * Закрытый конструктор, необходим для реализации шаблона
     * проектирования «Одиночка».
     */
    private function __construct($class, $params) {

        // все объекты приложения, экземпляр класса Register
        $this->register = Register::getInstance();
        // настройки приложения, экземпляр класса Config
        $this->config = Config::getInstance();
        // экземпляр класса Cache
        $this->cache = Cache::getInstance();
        // экземпляр класса базы данных
        $this->database = Database::getInstance();
        // кэширование данных разрешено?
        $this->enableDataCache = $this->config->cache->enable->data;

        /*
         * Этот код не имеет отношения к обычной работе приложения, когда роутер
         * анализирует строку $_SERVER['REQUEST_URI'], чтобы определить, какой
         * контроллер должен формировать страницу сайта. Передавая конструктору
         * параметры, можно принудительно задать имя класса контроллера, который
         * будет запущен в работу и параметры, передаваемые этому контроллеру.
         * Таким образом приложение можно запускать из командной строки для
         * формирования кэша, см. исходный код файла cache/make-cache.php.
         */
        if ( ! empty($class)) {
            /*
             * Имя класса контроллера: четыре части, разделенные символом подчеркивания,
             * например Category_Catalog_Frontend_Controller
             */
            $temp = explode('_', strtolower($class));
            if (class_exists($class)) { // такой класс существует?
                $this->controllerClassName = $class;
                $this->controller = $temp[1];
                $this->action = $temp[0];
                if ('backend' == $temp[1]) {
                    $this->backend = true;
                }
            } else {
                throw new Exception( 'Класс контроллера ' . $class . ' не найден');
            }
            $this->params = $params;
            return;
        }

        // запрос с использованием XmlHttpRequest?
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->xhr = true;
        }

        /*
         * Для того, чтобы через виртуальные адреса controller/action/params
         * можно было также передавать параметры через QUERY_STRING, необходимо
         * получить из $_SERVER['REQUEST_URI'] только компонент пути. Данные,
         * переданные через QUERY_STRING, также как и раньше, будут содержаться
         * в суперглобальных массивах $_GET и $_REQUEST.
         */
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // строка frontend/catalog/category/id/17

        // если включено кэширование данных
        if ($this->enableDataCache) {
            // получаем данные из кэша
            $data = $this->getCachedData($path);

            $this->xhr                 = $data['xhr'];
            $this->controller          = $data['controller'];
            $this->action              = $data['action'];
            $this->controllerClassName = $data['controllerClassName'];
            $this->params              = $data['params'];
            $this->backend             = $data['backend'];

            return;
        }

        $this->parseURL($path);

    }

    private function parseURL($path) {

        $path = trim($path, '/');
        if ('index.php' == strtolower($path) || '' == $path) {
            if ($this->xhr) {
                $this->controllerClassName = 'Xhr_' . $this->controllerClassName;
                if ( ! class_exists($this->controllerClassName)) { // такой класс существует?
                    $this->xhr = false;
                    $this->controller = 'notfound';
                    $this->controllerClassName = 'Index_Notfound_Frontend_Controller';
                }
            }
            return;
        }
        // в админке путь всегда начинается с backend
        if (preg_match('~^backend~i', $path)) {
            $this->backend = true;
        }
        // включена поддержка SEF (ЧПУ)?
        if ( (!$this->backend) && $this->config->sef->enable) {
            $path = $this->getURL($path);
            // контроллер не найден
            if (false === $path) {
                $this->setNotFound();
                return;
            }
        }
        // из $path извлекаем имя контроллера и действие
        $pattern = '~^(frontend|backend)/([a-z][a-z0-9]*)/([a-z][a-z0-9]*)~i';
        if ( ! preg_match($pattern, $path, $matches)) { // контроллер не найден
            $this->setNotFound();
            return;
        }
        $this->controller = strtolower($matches[2]);
        $this->action = strtolower($matches[3]);

        /*
         * Имя класса контроллера состоит из четырех частей, разделенных символом подчеркивания,
         * например, Index_Page_Frontend_Controller или Category_Catalog_Frontend_Controller;
         * исключение составляют контроллеры, обрабатывающие запросы с использованием объекта
         * XmlHttpRequest, например Xhr_Category_Catalog_Frontend_Controller
         */

        // получаем имя класса контроллера
        $frontback = ($this->backend) ? 'Backend' : 'Frontend';
        // составляем имя класса из четырех частей, разделенных символом подчеркивания
        $this->controllerClassName =
            ucfirst($this->action).'_'.ucfirst($this->controller).'_'.$frontback.'_Controller';
        if (class_exists($this->controllerClassName)) { // такой класс существует?

            // TODO: эта проверка из-за Menu_Catalog_Frontend_Controller, подумать

            // класс существует, абстрактный или нет?
            $reflection = new ReflectionClass($this->controllerClassName);
            if (!$this->xhr && $reflection->isAbstract()) { // класс абстрактный
                $this->setNotFound();
                return;
            }
        } else {
            $this->setNotFound();
            return;
        }

        // контроллер обрабытывает запрос типа XmlHttpRequest?
        if ($this->xhr) {
            $this->controllerClassName = 'Xhr_' . $this->controllerClassName;
        }
        if ( ! class_exists($this->controllerClassName)) { // такой класс существует?
            $this->setNotFound();
            return;
        }

        // получаем параметры
        $params = preg_replace($pattern, '', $path);
        if ( ! empty($params)) {
            $pattern = '~^(/([a-z][a-z0-9_]*)/([a-z0-9%+_.-]+))+$~i';
            if (preg_match($pattern, $params)) {
                $params = trim($params, '/');
                $temp = explode('/', $params);
                for ($i = 0; $i < count($temp); $i = $i + 2) {
                    $this->params[$temp[$i]] = rawurldecode($temp[$i+1]);
                }
            }
        }

    }

    private function getCachedData($path) {

        $xhr = $this->xhr ? 'true' : 'false';
        $key = __CLASS__ . '-' . md5($path) . '-xhr-' . $xhr;

        /*
         * Данные сохранены в кэше?
         */
        if ($this->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->cache->getValue($key);
        }

        /*
         * Данных в кэше нет, но другой процесс поставил блокировку и в
         * этот момент получает данные, чтобы записать их в кэш, нам надо
         * их только получить из кэша после снятия блокировки
         */
        if ($this->cache->isLocked($key)) {
            return $this->cache->getValue($key);
        }

        /*
         * Данных в кэше нет, блокировка не стоит, значит:
         * 1. ставим блокировку
         * 2. получаем данные
         * 3. записываем данные в кэш
         * 4. снимаем блокировку
         */
        $this->cache->lockValue($key);
        $this->parseURL($path);
        $data = array(
            'xhr'                 => $this->xhr,
            'controller'          => $this->controller,
            'action'              => $this->action,
            'controllerClassName' => $this->controllerClassName,
            'params'              => $this->params,
            'backend'             => $this->backend
        );
        $this->cache->setValue($key, $data);
        $this->cache->unlockValue($key);

        return $data;

    }

    /**
     * Возвращает имя класса контроллера
     */
    public function getControllerClassName() {
        return $this->controllerClassName;
    }

    /**
     * Возвращает название контроллера, например Catalog для
     * класса контроллера Category_Catalog_Frontend_Controller
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Возвращает название действия, например Category для
     * класса контроллера Category_Catalog_Frontend_Controller
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Возвращает массив параметров, которые будут переданы контроллеру
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Возвращает true, если идет работа с админкой
     */
    public function isBackend() {
        return $this->backend;
    }

    /**
     * Возвращает true, если запрос типа XmlHttpRequest
     */
    public function isXHR() {
        return $this->xhr;
    }

    /**
     * Функция принудительно устанавливает контроллер Index_Notfound_Frontend_Controller
     * или Index_Notfound_Backend_Controller; это происходит, если роутер не смог найти
     * класс контроллера после анализа $_SERVER['REQUEST_URI'] или если были переданы
     * некорректные параметры. См. комментарии в файлах app/controller/Base_Controller.php
     * и index.php.
     */
    public function setNotFound() {
        $this->xhr = false;
        $this->controller = 'notfound';
        $this->action = 'index';
        $frontback = ($this->backend) ? 'Backend' : 'Frontend';
        $this->controllerClassName = 'Index_Notfound_'.$frontback.'_Controller';
        $this->params = array();
    }

    /**
     * Функция вызывается, если запрошена страница общедоступной части сайта и в
     * настройках включена поддержка ЧПУ. Преобразует Search Engines Friendly =>
     * Controller/Action/Params, например about-company => frontend/page/index/id/7
     */
    private function getURL($path) {
        /*
         * сначала проверяем — существует ли в настройках правило преобразования
         * SEF->CAP, т.е. Search Engines Friendly => Controller/Action/Params; эти
         * правила описаны в файле app/config/routing.php
         */
        $sef2cap = $this->config->sef->sef2cap;
        foreach ($sef2cap as $key => $value) {
            if (preg_match($key, $path)) {
                return preg_replace($key, $value, $path);
            }
        }
        /*
         * если правило преобразования не найдено, пробуем найти $path среди
         * ЧПУ (SEF) страниц сайта, созданных администратором через админку
         */
        if ( ! preg_match('#^[a-z][-_0-9a-z]#i', $path)) {
            return false;
        }
        $query = "SELECT
                      `id`, `sefurl`
                  FROM
                      `pages`
                  WHERE
                      1";
        $pages = $this->database->fetchAll($query, array(), $this->enableDataCache);
        foreach ($pages as $page) {
            if ($path === $page['sefurl']) {
                return 'frontend/page/index/id/' . $page['id'];
            }
        }
        return false;
    }

    public function destroy() {
        self::$instance = null;
    }
}