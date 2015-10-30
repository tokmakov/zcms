<?php
/**
 * Класс Rmvprd_Basket_Frontend_Controller отвечает за удаление товара из корзины,
 * взаимодействует с моделью Basket_Frontend_Model, общедоступная часть сайта
 */
class Rmvprd_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление товара из корзины и редирект.
     */
    protected function input() {
        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }
        // удаляем товар из корзины
        $this->basketFrontendModel->removeFromBasket($this->params['id']);
        // редирект обратно на страницу корзины
        $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
    }

}