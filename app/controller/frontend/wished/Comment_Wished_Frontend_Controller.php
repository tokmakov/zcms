<?php
/**
 * Класс Comment_Wished_Frontend_Controller отвечает за добавление комментария
 * к отложенному товару, взаимодействует с моделью Wished_Frontend_Model,
 * общедоступная часть сайта
 */
class Comment_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция добавляет комментарий к отложенному товару и делает редирект
     * обратно на страницу со списком отложенных товаров с учетом постраничной
     * навигации
     */
    protected function input() {

        // данные должны быть отправлены методом POST
        if ( ! $this->isPostMethod()) {
            $this->notFoundRecord = true;
            return;
        }

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id']) && $_POST['product_id'] > 0)) {
            $this->notFoundRecord = true;
            return;
        } else {
            $product_id = (int)$_POST['product_id'];
        }

        $comment = '';
        if (isset($_POST['comment'])) {
            $comment = trim(utf8_substr($_POST['comment'], 0, 250));
        }

        // добавляем комментарий
        $this->wishedFrontendModel->addComment($product_id, $comment);

        // редирект обратно на страницу со списком отложенных товаров
        $url = 'frontend/wished/index';
        if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
            $url = $url . '/page/' . $_POST['page'];
        }
        $this->redirect($this->wishedFrontendModel->getURL($url));

    }

}