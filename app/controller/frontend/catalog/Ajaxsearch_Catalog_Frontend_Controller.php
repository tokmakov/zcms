<?php
/**
 * Класс Ajaxsearch_Catalog_Frontend_Controller формирует ответ на запрос
 * XmlHttpRequest в формате HTML, получает данные от модели Catalog_Frontend_Model,
 * общедоступная часть сайта. Ответ содержит результаты поиска по каталогу товаров
 */
class Ajaxsearch_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результаты поиска по каталогу товаров в формате HTML
     */
    private $output;


    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // получаем от модели массив результатов поиска
        $results = $this->catalogFrontendModel->getSearchResults($_POST['query'], 0, true, $_POST['time']);

        // формируем HTML результатов поиска
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/ajax/search.php',
            array(
                'results' => $results,
                'search'  => $_POST['query'],
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