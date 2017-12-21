<?php
/**
 * Абстрактный класс Base_Controller, родительский для всех контроллеров
 */
abstract class Base_Controller extends Base {

    /**
     * Переменная равна true, если работает контроллер Index_Notfound_Frontend_Controller
     * или Index_Notfound_Backend_Controller. Это происходит:
     * 1. Если роутер, анализируя строку $_SERVER['REQUEST_URI'], не смог найти класс
     *    контроллера, который должен формировать страницу, см. файл index.php.
     * 2. Роутер нашел класс контроллера, в файле index.php был создан экземпляр класса
     *    контроллера, но контроллеру были переданы некорректные параметры. Пример:
     *    frontend/page/index/id/12345, но страницы с уникальным id=12345 нет в таблице
     *    pages базы данных. Это возможно, если страница (новость, товар) была удалена
     *    или пользователь ошибся при вводе URL страницы. В этом случае происходит:
     *    - вызов request() из файла index.php, метод реализован в Base_Controller
     *    - вызов input() из request(), определен в Base_Controller, преопределен в
     *      Index_Page_Frontend_Controller
     *    - метод input() в Index_Page_Frontend_Controller обращается к модели, если запись
     *      в таблице БД не найдена, $notFoundRecord устанавливается в true и происходит
     *      возврат из метода
     *    - метод request(), сразу после вызова input(), проверяет значение $notFoundRecord;
     *      если true — происходит возврат из метода
     *    - в index.php, сразу после вызова request(), вызывается метод isNotFoundRecord(),
     *      который определен в Base_Controller
     *    - поскольку isNotFoundRecord() возвращает true, создается экземпляр класса
     *      контроллераIndex_Notfound_Frontend_Controller или Index_Notfound_Backend_Controller,
     *      которые сформируют страницу 404 Not Found
     *    - в конструкторе Index_Notfound_Frontend_Controller, Index_Notfound_Backend_Controller
     *      переменная $notFound устанавливается в true, что позволяет отправить заголовок при
     *      вызове метода sendHeaders(), который определен в Base_Controller и вызывается из
     *      файла index.php
     */
    protected $notFound = false;

    /**
     * Переменная равна true, если какому-либо контроллеру были переданы некорректные
     * параметры, см. комментарий выше.
     */
    protected $notFoundRecord = false;

    /**
     * запрос с использованием XmlHttpRequest?
     */
    protected $xhr = false;

    /**
     *  параметры, передаваемые контроллеру
     */
    protected $params;

    /**
     * содержимое тега title страницы, мета-тегов keywords и description
     */
    protected $title, $keywords, $description;

    /**
     * массивы переменных, которые будут переданы в шаблоны head.php,
     * header.php, menu.php, center.php, left.php, right.php, footer.php
     */
    protected $headVars   = array(), $headerVars = array(), $menuVars  = array(),
              $centerVars = array(), $leftVars   = array(), $rightVars = array(),
              $footerVars = array();

    /**
     * HTML-код всей страницы, который формируется так:
     * 1. из файла index.php вызывается метод контроллера request(), который реализован в
     *    классе Base_Controller
     * 2. метод request() вызывает последовательно методы input() и output()
     * 3. метод input(), который реализован в Base_Controller и переопределен в дочерних
     *    классах:
     *    - получает от модели данные, необходимые для формирования страницы, и сохраняет
     *      их в переменных $headVars, $headerVars, $menuVars,  $centerVars, $leftVars,
     *      $rightVars, $footerVars
     *    - получает имена файлов шаблонов отдельных частей страницы и сохраняет их в
     *      переменных $headTemplateFile, $headerTemplateFile, $menuTemplateFile,
     *      $centerTemplateFile, $leftTemplateFile, $rightTemplateFile, $footerTemplateFile
     * 4. метод output(), который реализован в дочерних классах, вызывает метод render(),
     *    передавая ему полученные от модели данные и имена файлов шаблонов
     *    $this->headContent   = $this->render($this->headTemplateFile, $this->headVars);
     *    $this->headerContent = $this->render($this->headerTemplateFile, $this->headerVars);
     *    $this->menuContent   = $this->render($this->menuTemplateFile, $this->menuVars);
     *    ..........
     *    и т.д.
     * 5. метод render() «прогоняет» данные через шаблон и возвращает сформированный html-код
     * 6. метод output(), который реализован в дочерних классах, в самом конце еще раз вызывает
     *    метод render(), чтобы произвести окончательную сборку страницы из отдельных частей
     *    html-кода
     *    $this->pageContent = $this->render($this->wrapperTemplateFile, array(...));
     */
    protected $pageContent;

    /**
     * переменные хранят html-код отдельных частей страницы
     */
    protected $headContent, $headerContent, $menuContent, $centerContent,
              $leftContent, $rightContent, $footerContent;

    /**
     * полный путь к главному файлу шаблона wrapper.php
     */
    protected $wrapperTemplateFile;

    /**
     * полный путь путь к файлам шаблонов отдельных частей страницы
     */
    protected $headTemplateFile, $headerTemplateFile, $menuTemplateFile,
              $centerTemplateFile, $leftTemplateFile, $rightTemplateFile,
              $footerTemplateFile;

    /**
     * массивы CSS и JS файлов, подключаемых к странице
     */
    protected $cssFiles, $jsFiles;


    public function __construct($params = null) {
        parent::__construct();
        // параметры, передававаемые контроллеру
        $this->params = $params;
        // запрос с использованием XmlHttpRequest?
        $this->xhr = Router::getInstance()->isXHR();
    }

    /**
     * Функция реализована в дочерних классах, там она получает от модели данные,
     * необходимые для формирования страницы
     */
    protected function input() {
        /*
         * задать пути к файлам шаблонов и пути к подключаемым css и js файлам;
         * если запрос с использованием XmlHttpRequest, пути к файлам шаблонов
         * задавать не нужно и css, js файлы подключать не надо, потому как
         * страница целиком не будет формироваться
         */
        if ( ! $this->xhr) {
            $this->setCssJsTemplateFiles();
        }
    }

    /**
     * Функция реализована в дочерних классах, где происходит следующее:
     * 1. формируется html-код отдельных частей страницы с помощью метода render()
     * 2. собирается страница целиком из отдельных частей с помощью метода render()
     */
    abstract protected function output();

    /**
     * Функция формирует страницу: сначала в методе input() дочернего класса
     * получаем от модели данные, необходимые для формирования страницы, затем
     * в методе output() дочернего класса:
     * 1. формируется html-код отдельных частей страницы с помощью метода render()
     * 2. собирается страница целиком из отдельных частей с помощью метода render()
     */
    public function request() {

        /*
         * Получение от модели данных, необходимых для формирования страницы
         */
        $this->input();

        /*
         * Запись не найдена в таблице БД, дальше формировать страницу нет смысла,
         * все равно будет вызван контроллер Index_Notfound_Frontend_Controller
         * или Index_Notfound_Backend_Controller
         */
        if ($this->notFoundRecord) {
            return;
        }

        /*
         * Формирование отдельных частей страницы (шапка, меню, основной контент,
         * левая и правая колонка, подвал)
         */
        $this->output();

    }

    /**
     * Функция возвращает html-код сформированной страницы
     */
    public function getPageContent() {
        return $this->pageContent;
    }

    /**
     * Функция возвращает размер страницы в байтах для отправки заголовка
     * Content-Length
     */
    private function getContentLength() {
        return strlen($this->pageContent);
    }

    /**
     * Функция для обработки шаблонов, принимает имя файла шаблона и массив
     * переменных, которые должны быть доступны в шаблоне, возвращает html-код
     * шаблона с подставленными значениями переменных
     */
    protected function render($template, $params = array()) {
        if ( ! is_file($template)) {
            throw new Exception(
                'Не найден файл шаблона ' . $template . ', контроллер ' . get_class($this)
            );
        }
        extract($params);
        ob_start();
        require $template;
        return ob_get_clean();
    }

    /**
     * Функция отправляет заголовки Content-Type, Content-Length
     */
    public function sendHeaders() {
        if ($this->notFound) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    /**
     * Функция возвращает true, если какому-либо контроллеру, например
     * Index_Page_Frontend_Controller, были переданы некорректные параметры.
     * Пример: frontend/page/id/12345, но страницы с уникальным id=12345
     * нет в таблице pages базы данных. Это возможно, если страница (новость,
     * товар) была удалена или пользователь ошибся при вводе URL страницы.
     */
    public function isNotFoundRecord() {
        return $this->notFoundRecord;
    }

    /**
     * Функция возвращает true, если идет работа с административной частью сайта
     */
    public function isBackend() {
        return $this->backend;
    }

    /**
     * Функция задает пути к файлам шаблонов и заполняет два массива:
     * 1. массив $this->cssFiles с путями подключаемых к странице css-файлов
     * 2. массив $this->jsFiles с путями подключаемых к странице js-файлов
     */
    private function setCssJsTemplateFiles() {

        $router     = Router::getInstance();
        $controller = $router->getController();
        $action     = $router->getAction();

        /*
         * Как поключаются css и js файлы? Сначала подключаются базовые файлы, т.е. те файлы, которые
         * должны быть подключены ко всем страницам сайта. Дальше подключаются файлы, заданные для
         * родительского класса, например для абстрактного класса Catalog_Frontend_Controller. Наконец,
         * подключаются файлы, заданные для этого класса, например, Product_Catalog_Frontend_Controller
         *
         * Пример подключения CSS-файлов (см. файлы app/config/css.php и app/config/js.php):
         * 'css' => array(                         // CSS файлы, подключаемые к странице
         *     'frontend' => array(                // общедоступная часть сайта
         *         'base' => array(                // css-файлы, подключаемые ко всем страницам сайта
         *             'reset.css',
         *             'common.css',
         *         ),
         *         'index' => 'jquery.slider.css', // только для главной страницы, формируемой Index_Index_Frontend_Controller
         *         'page' => 'page.css',           // для страниц, которые формирует Index_Page_Frontend_Controller
         *         'catalog' => 'catalog.css',     // для страниц, которые формируют дочерние классы Catalog_Frontend_Controller
         *         'catalog-product' => array(     // только для страниц, которые формирует Product_Catalog_Frontend_Controller
         *             'product.css',
         *             'jquery.lightbox.css',
         *         ),
         *     ),
         *     'backend' => array(                 // административная часть сайта
         *         ..........
         *     ),
         * )
         *
         * Здесь важно понимать, что у некоторых абстактных классов есть только один дочерний класс,
         * например: Page_Frontend_Controller и Index_Page_Frontend_Controller. А у других абстрактных
         * классов есть несколько дочерних классов, например у Catalog_Frontend_Controller:
         * 1. Index_Catalog_Frontend_Controller (главная страница каталога)
         * 2. Product_Catalog_Frontend_Controller (страница товара каталога)
         * 3. Category_Catalog_Frontend_Controller (страница категории каталога)
         * 4. Maker_Catalog_Frontend_Controller (страница производителя каталога)
         *
         * Запись вида
         *   'catalog' => 'catalog.css', // для всех страниц каталога
         *   'catalog-index' => 'catalog-index.css' // только для главной страницы каталога
         * имеет смысл, а запись вида
         *   'page' => 'page.css'
         *   'page-index' => 'lightbox.css'
         * не будет ошибочной, но сбивает с толку. Сбивает с толку потому, что подразумевает,
         * что page.css подключается для всех дочерних классов Page_Frontend_Controller. Но у
         * Page_Frontend_Controller только один дочерний класс, поэтому либо так
         * 'page' => array(
         *     'page.css',
         *     'lightbox.css'
         * )
         * либо так
         * 'page-index' => array(
         *     'page.css',
         *     'lightbox.css'
         * )
         */

        // задать базовые css и js файлы, которые подключаются всегда (ко всем страницам сайта)
        $this->setCssJsFiles('base');
        // задать css и js файлы, которые подключаются для абстактного родительского класса,
        // а точнее, для всех потомков суперкласса; это случай Catalog_Frontend_Controller или
        // Page_Frontend_Controller
        $this->setCssJsFiles($controller);
        if (isset($this->params['id'])) { // TODO: что вообще здесь происходит?
            $this->setCssJsFiles($controller . '-' . $this->params['id']);
        }
        // задать css и js файлы, которые подключаются только для этого класса; это случай
        // Category_Catalog_Frontend_Controller или Index_Page_Frontend_Controller
        $this->setCssJsFiles($controller . '-' . $action);
        if (isset($this->params['id'])) {
            $this->setCssJsFiles($controller . '-' . $action . '-' . $this->params['id']);
        }

        /*
         * Eсли для контроллера Index_Page_Frontend_Controller существует файл
         * view/example/frontend/template/page/wrapper.php, то будет использован
         * именно он, а не view/example/frontend/template/wrapper.php.
         * Если существует view/example/frontend/template/page/index/wrapper.php, то
         * будет использован он, а не view/example/frontend/template/page/wrapper.php.
         * Аналогично для файлов header.php, menu.php, center.php, left.php и т.д.
         *
         * Т.е. шаблоны по умолчанию, расположенные в папке view/example/frontend/template,
         * переопределяются шаблонами, расположенными глубже в иерархии директорий.
         * Шаблон по умолчанию view/example/frontend/template/wrapper.php будет
         * переопределен шаблоном view/example/frontend/template/catalog/wrapper.php. А
         * шаблон view/example/frontend/template/catalog/wrapper.php, в свою очередь, будет
         * переопределен шаблоном view/example/frontend/template/catalog/product/wrapper.php.
         */

        // путь к файлам шаблонов по умолчанию
        $backfront = ($this->backend) ? 'backend' : 'frontend';
        $templatePath = $this->config->site->theme . '/' . $backfront . '/template';
        // установить файлы шалонов по умолчанию
        $this->setTemplateFiles($templatePath);

        // теперь переопределяем шаблоны по умолчанию
        if (is_dir($templatePath . '/' . $controller)) { // если существует директория с дочерними шаблонами
            $templatePath = $templatePath . '/' . $controller;
            // путь к файлам шаблонов текущей страницы (переопределяем файлы шалонов по умолчанию)
            $this->setTemplateFiles($templatePath);
            if (is_dir($templatePath . '/' . $action)) { // если существует директория с дочерними-дочерними шаблонами
                $templatePath = $templatePath . '/' . $action;
                // путь к файлам шаблонов текущей страницы (переопределяем файлы родительского шаблона)
                $this->setTemplateFiles($templatePath);
                // если некая сущность (страница, товар, новость) имеет уникальный
                // идентификатор, для нее может быть задан индивидуальный шаблон, например
                // view/example/frontend/template/catalog/product/12345/center.php
                if (isset($this->params['id']) && is_dir($templatePath . '/' . $this->params['id'])) {
                     $templatePath = $templatePath . '/' . $this->params['id'];
                     $this->setTemplateFiles($templatePath);
                }
            }
        }

    }

    /**
     * Функция задает пути к подключаемым css и js файлам, т.е. заполняет два массива:
     * 1. массив $this->cssFiles с путями подключаемых к странице css-файлов
     * 2. массив $this->jsFiles с путями подключаемых к странице js-файлов
     */
    private function setCssJsFiles($name) {

        /*
         * работаем с админкой сайта?
         */
        $backfront = 'frontend';
        if ($this->backend) {
            $backfront = 'backend';
        }

        /*
         * подключаемые css файлы
         */
        $host = $this->config->site->url;
        // Content Delivery Network только для общедоступной части сайта
        if ( ! $this->backend && $this->config->cdn->enable->css) {
            $host = $this->config->cdn->url;
        }
        if (isset($this->config->css->$backfront->$name)) {
            $temp = $this->config->css->$backfront->$name;
            if (is_object($temp)) { // подключаем несколько файлов
                foreach ($temp as $file) {
                    /*
                     * если это внешний файл, то ссылка на файл может быть либо с указанием протокола,
                     * например http://dadata.ru/static/css/lib/suggestions-16.1.css, либо без указания
                     * протокола, например //dadata.ru/static/css/lib/suggestions-16.1.css
                     */
                    if ('http' == substr($file, 0, 4) || '//' == substr($file, 0, 2)) {
                        $this->cssFiles[] = $file;
                        continue;
                    }
                    $fileName = $this->config->site->theme . '/' . $backfront . '/resource/css/' . $file;
                    if ( ! is_file($fileName)) {
                        throw new Exception('Файл ' . $fileName . ' не найден');
                    }
                    $this->cssFiles[] = $host . $fileName;
                }
            } else { // подключаем один файл
                /*
                 * если это внешний файл, то ссылка на файл может быть как с указанием, так и
                 * без указания протокола; см. комментарий выше
                 */
                if ('http' == substr($temp, 0, 4) || '//' == substr($temp, 0, 2)) {
                    $this->cssFiles[] = $temp;
                } else {
                    $fileName = $this->config->site->theme . '/' . $backfront . '/resource/css/' . $temp;
                    if ( ! is_file($fileName)) {
                        throw new Exception('Файл ' . $fileName . ' не найден');
                    }
                    $this->cssFiles[] = $host . $fileName;
                }
            }
        }

        /*
         * подключаемые js файлы
         */
        $host = $this->config->site->url;
        // Content Delivery Network только для общедоступной части сайта
        if (!$this->backend && $this->config->cdn->enable->js) {
            $host = $this->config->cdn->url;
        }
        if (isset($this->config->js->$backfront->$name)) {
            $temp = $this->config->js->$backfront->$name;
            if (is_object($temp)) { // подключаем несколько файлов
                foreach ($temp as $file) {
                    /*
                     * если это внешний файл, то ссылка на файл может быть либо с указанием протокола,
                     * например http://code.jquery.com/jquery-latest.min.js, либо без указания протокола,
                     * например //code.jquery.com/jquery-latest.min.js
                     */
                    if ('http' == substr($file, 0, 4) || '//' == substr($file, 0, 2)) {
                        $this->jsFiles[] = $file;
                        continue;
                    }
                    $fileName = $this->config->site->theme . '/' . $backfront . '/resource/js/' . $file;
                    if ( ! is_file($fileName)) {
                        throw new Exception('Файл ' . $fileName . ' не найден');
                    }
                    $this->jsFiles[] = $host . $fileName;
                }
            } else { // подключаем один файл
                /*
                 * если это внешний файл, то ссылка на файл может быть как с указанием, так и
                 * без указания протокола; см. комментарий выше
                 */
                if ('http' == substr($temp, 0, 4) || '//' == substr($temp, 0, 2)) {
                    $this->jsFiles[] = $temp;
                    return;
                }
                $fileName = $this->config->site->theme . '/' . $backfront . '/resource/js/' . $temp;
                if ( ! is_file($fileName)) {
                    throw new Exception('Файл ' . $fileName . ' не найден');
                }
                $this->jsFiles[] = $host . $fileName;
            }
        }

    }


    /**
     * Функция задает пути к файлам шаблонов wrapper.php, head.php, header.php, menu.php,
     * center.php, left.php, right.php, footer.php
     */
    private function setTemplateFiles($path) {
        $templates = array('wrapper', 'head', 'header', 'menu', 'center', 'left', 'right', 'footer');
        foreach($templates as $name) {
            $file = $path . '/' . $name . '.php';
            if (is_file($file)) {
                $temp = $name . 'TemplateFile';
                $this->$temp = $file;
            }
        }
    }

}