<?php
/**
 * Класс Xhr_Makers_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате HTML, получает данные от модели Maker_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результаты поиска производителя среди всех производителей
 */
class Xhr_Makers_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результаты поиска производителя в формате HTML
     */
    private $output;


    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

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

        // получаем от модели массив результатов поиска
        $result = $this->makerCatalogFrontendModel->getMakerSearchResult($_POST['query'], $sort, $perpage);

        // формируем HTML результатов поиска
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/makers.php',
            array(
                'result' => $result,
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