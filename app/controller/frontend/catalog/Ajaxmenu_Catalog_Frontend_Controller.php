<?php
/**
 * Класс Ajaxmenu_Catalog_Frontend_Controller формирует ответ на запрос
 * XmlHttpRequest в формате HTML, получает данные от модели Catalog_Frontend_Model,
 * общедоступная часть сайта. Ответ содержит дочерние категории для элемента меню.
 */
class Ajaxmenu_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * дочерние категории для элемента меню в формате HTML
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

        sleep(2);

        // если не передан id категории или id категории не число
        if ( ! (isset($_POST['id']) && ctype_digit($_POST['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $id= (int)$_POST['id'];
        }

        // получаем от модели массив дочерних категорий
        $childs = $this->catalogFrontendModel->getCategoryChilds($id);

        // формируем HTML
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/ajax/menu.php',
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