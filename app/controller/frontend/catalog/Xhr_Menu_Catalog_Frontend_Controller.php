<?php
/**
 * Класс Xhr_Menu_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате HTML, получает данные от модели Menu_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит дочерние категории для элемента меню каталога в левой
 * колонке
 */
class Xhr_Menu_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * дочерние категории для элемента меню в формате HTML
     */
    private $output;


    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    /**
     * Переопределяем метод Base_Controller::request(), потому что здесь нам не
     * нужен сложный алгоритм формирования страницы сайта. Мы просто запрашиваем
     * данные у модели и прогоняем их через шаблон, чтобы получить фрагмент html
     * кода.
     */
    public function request() {

        // если не передан id категории или id категории не число
        if ( ! (isset($_POST['id']) && ctype_digit($_POST['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        $id = (int)$_POST['id'];

        // пользователь выбрал сортировку товаров?
        $sort = 0;
        if (isset($_COOKIE['sort']) && in_array($_COOKIE['sort'], array(1,2,3,4,5,6))) {
            $sort = (int)$_COOKIE['sort'];
        }

        // пользователь выбрал кол-во товаров на странице?
        $perpage = 0;
        $others = $this->config->pager->frontend->products->getValue('others'); // доступные варианты
        if (isset($_COOKIE['perpage']) && in_array($_COOKIE['perpage'], $others)) {
            $perpage = (int)$_COOKIE['perpage'];
        }

        // получаем от модели массив дочерних категорий
        $childs = $this->menuCatalogFrontendModel->getCategoryChilds($id, $sort, $perpage);

        // формируем HTML
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/menu.php',
            array(
                'childs' => $childs,
            )
        );
    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-type: text/html; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}