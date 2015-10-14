<?php
/**
 * Класс Repeat_User_Frontend_Controller отвечает за повторение заказа, т.е.
 * добавляет в корзину все товары ранее сделанного заказа, взаимодействует с
 * моделью User_Frontend_Model, общедоступная часть сайта
 */
class Repeat_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, нужно только получить
     * список товаров ранее сделанного заказа и добавить их в корзину. И редирект
     * обратно на страницу со списком заказов пользователя.
     */
    protected function input() {

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // данные должны быть отправлены методом POST
        if (!$this->isPostMethod()) {
            $this->notFoundRecord = true;
            return;
        }

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // добавляем товары в корзину
        $this->userFrontendModel->repeatOrder($this->params['id']);

        // редирект на страницу списка заказов
        $url = 'frontend/user/allorders';
        if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
            $url = $url . '/page/' . $_POST['page'];
        }
        $this->redirect($this->userFrontendModel->getURL($url));

    }
}