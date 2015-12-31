<?php
/**
 * Класс Clear_Basket_Frontend_Controller отвечает за удаление всех товаров
 * из корзины, взаимодействует с моделью Basket_Frontend_Model, общедоступная
 * часть сайта
 */
class Clear_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление товаров из корзины и редирект.
     */
    protected function input() {
        // удаляем все товары из корзины
        $this->basketFrontendModel->clearBasket();
        // редирект обратно на страницу корзины
        $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
    }

}