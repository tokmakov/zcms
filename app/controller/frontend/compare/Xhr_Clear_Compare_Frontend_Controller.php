<?php
/**
 * Класс Xhr_Clear_Compare_Frontend_Controller принимает запрос XmlHttpRequest,
 * удаляет все товары из сравнения, работает с моделью Compare_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Clear_Compare_Frontend_Controller extends Compare_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // добавляем товар в список сравнения
        $this->compareFrontendModel->clearCompareList();

        // получаем html-код списка товаров, отложенных для сравнения (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/compare/xhr/compare.php',
            array(
                'sideCompareProducts' => array(),
                'indexCompareURL'     => $this->compareFrontendModel->getURL('frontend/compare/index'),
                'clearCompareURL'     => $this->compareFrontendModel->getURL('frontend/compare/clear'),
            )
        );
    }

}