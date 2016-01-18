<?php
/**
 * Класс Router анализирует строку $_SERVER['REQUEST_URI'] и позволяет
 * определить, какой контроллер должен формировать страницу сайта;
 * реализует шаблон проектирования «Одиночка»
 */
class Router {

    /**
     * для хранения единственного экземпляра данного класса
     */
    private static $instance;
    
    /**
     * запрос с использованием XmlHttpRequest?
     */
    protected $xhr = false;

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
     * для хранения всех объектов приложения, экземпляр класса Register
     */
    protected $register;

    /**
     * настройки приложения, экземпляр класса Config
     */
    protected $config;
    
    /**
     * для хранения экземпляра класса базы данных Database
     */
    protected $database;


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
        // экземпляр класса базы данных
        $this->database = Database::getInstance();

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
         * Для того, что бы через виртуальные адреса controller/action/params
         * можно было также передавать параметры через QUERY_STRING, необходимо
         * получить из $_SERVER['REQUEST_URI'] только компонент пути. Данные,
         * переданные через QUERY_STRING, также как и раньше будут содержаться
         * в суперглобальных массивах $_GET и $_REQUEST.
         */
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // строка frontend/catalog/category/id/17
        $path = trim($path, '/');
        if ('index.php' == strtolower($path) || '' == $path) {
            return;
        }
        // в админке путь всегда начинается с backend
        if (preg_match('~^backend~i', $path)) {
            $this->backend = true;
        }
        // включена поддержка SEF (ЧПУ)?
        if ( (!$this->backend) && $this->config->sef->enable) {
            $path = $this->getURL($path);
            if (false === $path) {
                $this->controller = 'notfound';
                $frontback = ($this->backend) ? 'Backend' : 'Frontend';
                $this->controllerClassName = 'Index_Notfound_' . $frontback . '_Controller';
                return;
            }
        }
        // из $path извлекаем имя контроллера и действие
        $pattern = '~^(frontend|backend)/([a-z][a-z0-9]*)/([a-z][a-z0-9]*)~i';
        if ( ! preg_match($pattern, $path, $matches)) {
            $this->controller = 'notfound';
            $frontback = ($this->backend) ? 'Backend' : 'Frontend';
            $this->controllerClassName = 'Index_Notfound_' . $frontback . '_Controller';
            return;
        }
        $this->controller = strtolower($matches[2]);
        $this->action = strtolower($matches[3]);

        /*
         * Имя класса состоит из четырех частей, разделенных символом подчеркивания,
         * например, Index_Page_Frontend_Controller или Index_Page_Frontend_Controller
         */

        // получаем имя класса контроллера
        $frontback = ($this->backend) ? 'Backend' : 'Frontend';
        // составляем имя класса из четырех частей, разделенных символом подчеркивания
        $this->controllerClassName = ucfirst($this->action).'_'.ucfirst($this->controller).'_'.$frontback.'_Controller';
        if ( ! class_exists($this->controllerClassName)) { // такой класс существует?
            $this->controller = 'notfound';
            $this->action = 'index';
            $this->controllerClassName = 'Index_Notfound_' . $frontback . '_Controller';
            return;
        }
        
        if ($this->xhr) {
            $this->controllerClassName = 'Xhr_' . $this->controllerClassName;
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

    /**
     * Возвращает имя класса контроллера
     */
    public function getControllerClassName() {
        return $this->controllerClassName;
    }

    /**
     * Возвращает название контроллера
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Возвращает название действия (экшен)
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
     * Для случая NotFoundRecord (см. index.php и Base_Controller.php)
     */
    public function setNotFound() {
        $this->controller = 'notfound';
        $this->action = 'index';
        $frontback = ($this->backend) ? 'Backend' : 'Frontend';
        $this->controllerClassName = 'Index_Notfound_'.$frontback.'_Controller';
        $this->params = array();
    }
    
    private function getURL($path) {
        // если не включено кэширование данных
        if ( ! $this->config->cache->enable->data) {
            return $this->URL($path);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . $path;

        /*
         * данные сохранены в кэше?
         */
        if ($this->register->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->register->cache->getValue($key);
        }

        /*
         * данных в кэше нет, но другой процесс поставил блокировку и в этот
         * момент получает данные отRender::URL(), чтобы записать их в кэш,
         * нам надо их только получить из кэша после снятия блокировки
         */
        if ($this->register->cache->isLocked($key)) {
            try {
                // получаем данные из кэша
                return $this->register->cache->getValue($key);
            } catch (Exception $e) {
                /*
                 * другой процесс поставил блокировку, попытался получить данные от
                 * Render::URL() и записать их в кэш; если по каким-то причинам это
                 * не получилось сделать, мы здесь будем пытаться читать из кэша
                 * значение, которого не существует или оно устарело
                 */
                throw $e;
            }
        }

        /*
         * данных в кэше нет, блокировка не стоит, значит:
         * 1. ставим блокировку
         * 2. получаем данные
         * 3. записываем данные в кэш
         * 4. снимаем блокировку
         */
        $this->register->cache->lockValue($key);
        try {
            $result = $this->URL($path);
            $this->register->cache->setValue($key, $result);
        } finally {
            $this->register->cache->unlockValue($key);
        }
        // возвращаем результат
        return $result;
    }

    private function URL($path) {
        $sef2cap = $this->config->sef->sef2cap;
        foreach ($sef2cap as $key => $value) {
            if (preg_match($key, $path)) {
                return preg_replace($key, $value, $path);
            }
        }
        // получаем все страницы
        if ( ! preg_match('#^[a-z][-_0-9a-z]#i', $path)) {
            return false;
        }
        $query = "SELECT
                      `id`, `sefurl`
                  FROM
                      `pages`
                  WHERE
                      1";
        $pages = $this->database->fetchAll($query);
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