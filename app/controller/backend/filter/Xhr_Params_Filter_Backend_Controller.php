<?php
/**
 * Класс Xhr_Params_Filter_Backend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате HTML, получает данные от модели Filter_Backend_Model, административная
 * часть сайта. Ответ содержит список всех параметров и значений для выбранной
 * функциональной группы.
 */
class Xhr_Params_Filter_Backend_Controller extends Filter_Backend_Controller {

    /**
     * список всех параметров и значений в формате HTML
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

        $group = 0; // идентификатор функциональной группы
        if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
            $group = (int)$_POST['group'];
        }

        $product = 0; // идентификатор товара
        if (isset($_POST['product']) && ctype_digit($_POST['product'])  && $_POST['product'] > 0) {
            $product = (int)$_POST['product'];
        }

        // получаем от модели массив параметров, привязанных к группе и массивы
        // привязанных к этим параметрам значений
        $allParams = $this->catalogBackendModel->getGroupParams($group);

        // получаем от модели массив параметров, привязанных к товару и массивы
        // привязанных к этим параметрам значений
        $params = $this->catalogBackendModel->getProductParams($product);

        // формируем HTML
        $this->output = $this->render(
            $this->config->site->theme . '/backend/template/filter/xhr/params.php',
            array(
                'allParams' => $allParams,
                'params'    => $params,
            )
        );

    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-type: application/json; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}