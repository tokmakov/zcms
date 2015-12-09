<?php
/**
 * Класс Comment_Wished_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет или обновляет комментарий к товар в списоке избанных, работает
 * с моделью Wished_Frontend_Model, общедоступная часть сайта
 */
class Comment_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $product_id = (int)$_POST['product_id'];
        }

        $comment = '';
        if (isset($_POST['comment'])) {
            $comment = trim(utf8_substr($_POST['comment'], 0, 250));
        }

        // добавляем комментарий
        $this->wishedFrontendModel->addComment($product_id, $comment);

        $this->pageContent = 'Комментарий сохранен';

    }

}