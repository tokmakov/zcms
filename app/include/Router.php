<?php
/**
 * Класс Router анализирует строку $_SERVER['REQUEST_URI'] и позволяет
 * определить, какой контроллер должен формировать страницу сайта.
 * Реализует шаблон проектирования «Одиночка»
 */
class Router {

    /**
     * для хранения единственного экземпляра данного класса
     */
    private static $instance;

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
    private $controllerClassName = 'Index_Frontend_Controller';

    /**
     * массив параметров, которые будут переданы контроллеру
     */
    private $params = array();

    /**
     * идет работа с админкой?
     */
    private $backend = false;

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

        /*
         * Этот код не имеет отношения к обычной работе приложения, когда роутер
         * анализирует строку $_SERVER['REQUEST_URI'], чтобы определить, какой
         * контроллер должен формировать страницу сайта. Передавая конструктору
         * параметры, можно принудительно задать имя класса контроллера, который
         * будет запущен в работу и параметры, передаваемые этому контроллеру.
         * Таким образом приложение можно запускать из командной строки для
         * формирования кэша, см. исходный код файла cache/make-cache.php.
         */        
        if (!empty($class)) {
            $temp = explode('_', strtolower($class));
            /*
             * Имя класса будет:
             * 1. Page_Frontend_Controller: три части, разделенные символом подчеркивания
             * 2. Category_Catalog_Frontend_Controller: четыре части, разделенные символом
             *    подчеркивания
             */
            if (count($temp) == 3) { // первый случай, Page_Frontend_Controller
                if (class_exists($class)) { // такой класс существует?
                    // класс существует, абстрактный или нет?
                    $reflection = new ReflectionClass($this->controllerClassName);
                    if ($reflection->isAbstract()) { // класс абстрактный
                        $class = 'Index_' . $class;
                        if (!class_exists($class)) { // такой класс существует?
                            throw new Exception( 'Класс контроллера ' . $class . ' не найден');
                        }
                    }
                    $this->controllerClassName = $class;
                    $this->controller = $temp[0];
                    if ('backend' == $temp[1]) {
                        $this->backend = true;
                    }
                } else {
                    throw new Exception( 'Класс контроллера ' . $class . ' не найден');
                }
            } elseif (count($temp) == 4) { // второй случай, Category_Catalog_Frontend_Controller
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
            } else {
                throw new Exception( 'Класс контроллера ' . $class . ' не найден');
            }
            $this->params = $params;
            return;
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
        if (!$this->backend && Config::getInstance()->sef->enable) {
            $path = $this->getCapUrl($path);
            if (is_null($path)) {
                $this->controller = 'notfound';
                $frontback = ($this->backend) ? 'Backend' : 'Frontend';
                $this->controllerClassName = 'Notfound_' . $frontback . '_Controller';
                return;
            }
        }
        // из $path извлекаем имя контроллера и действие
        $pattern = '~^(frontend|backend)/([a-z][a-z0-9]*)/([a-z][a-z0-9]*)~i';
        if (!preg_match($pattern, $path, $matches)) {
            $this->controller = 'notfound';
            $frontback = ($this->backend) ? 'Backend' : 'Frontend';
            $this->controllerClassName = 'Notfound_' . $frontback . '_Controller';
            return;
        }
        $this->controller = strtolower($matches[2]);
        $this->action = strtolower($matches[3]);

        /*
         * Каким будет имя класса контроллера? Тут возможны два варианта:
         * 1. Существует абстрактный класс Catalog_Frontend_Controller, у которого
         *    есть несколько дочерних классов: Product_Catalog_Frontend_Controller,
         *    Category_Catalog_Frontend_Controller, Maker_Catalog_Frontend_Controller
         *    и т.п. Имя класса состоит из четырех частей, разделенных символом
         *    подчеркивания.
         * 2. Существует не абстрактный класс Page_Frontend_Controller. Это частный
         *    случай первого варианта. Потому как правильно было бы так: абстрактный
         *    класс Page_Frontend_Controller и его единственный дочерний класс
         *    Index_Page_Frontend_Controller. Но допускается создание не абстрактного
         *    класса Page_Frontend_Controller, у которого не будет дочернего класса
         *    Index_Page_Frontend_Controller. Имя класса состоит из трех частей,
         *    разделенных символом подчеркивания.
         */

        // получаем имя класса контроллера
        $frontback = ($this->backend) ? 'Backend' : 'Frontend';
        // составляем имя класса из трех частей, разделенных символом
        // подчеркивания; класс будет либо абстрактным (первый вариант),
        // либо не абстрактным (второй вариант)
        $this->controllerClassName = ucfirst($this->controller).'_'.$frontback.'_Controller';
        if (class_exists($this->controllerClassName)) { // такой класс существует?
            // класс существует, абстрактный или нет?
            $reflection = new ReflectionClass($this->controllerClassName);
            if ($reflection->isAbstract()) { // класс абстрактный, первый вариант
                // составляем имя класса из четырех частей, разделенных символом подчеркивания
                $this->controllerClassName = ucfirst($this->action).'_'.ucfirst($this->controller).'_'.$frontback.'_Controller';
                if (!class_exists($this->controllerClassName)) {  // такого дочернего класса нет
                    $this->controller = 'notfound';
                    $this->action = 'index';
                    $this->controllerClassName = 'Notfound_'.$frontback.'_Controller';
                    return;
                }
            } else { // класс не абстрактный, второй вариант
                if ('index' !== $this->action) {
                    $this->controller = 'notfound';
                    $this->action = 'index';
                    $this->controllerClassName = 'Notfound_'.$frontback.'_Controller';
                    return;
                }
            }
        }

        // получаем параметры
        $params = preg_replace($pattern, '', $path);
        if (!empty($params)) {
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
        $this->controllerClassName = 'Notfound_'.$frontback.'_Controller';
        $this->params = array();
    }

    private function getCapUrl($path) {
        $sef2cap = Config::getInstance()->sef->sef2cap;
        foreach($sef2cap as $key => $value) {
            if (preg_match($key, $path)) {
                return preg_replace($key, $value, $path);
            }
        }
        /*
        // если это страница, у нее свой SEF URL (ЧПУ), которого нет в массиве $config->sef->sef2cap
        $model = new Page_Frontend_Model();
        $pages = $model->getAllPages();
        foreach($pages as $page) {
            if (preg_match()) {
                return preg_replace();
            }
        }
        */
        return null;
    }
    
    public function destroy() {
        self::$instance = null;
    }
}