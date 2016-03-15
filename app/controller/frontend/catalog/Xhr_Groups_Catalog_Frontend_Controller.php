<?php
/**
 * Класс Xhr_Groups_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате HTML, получает данные от модели Group_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результаты поиска по функционалу
 */
class Xhr_Groups_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результаты поиска по функционалу в формате HTML
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

        // получаем от модели массив результатов поиска
        $result = $this->groupCatalogFrontendModel->getGroupSearchResult($_POST['query']);

        // формируем HTML результатов поиска
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/groups.php',
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