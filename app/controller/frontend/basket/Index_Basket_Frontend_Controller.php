<?php
/**
 * Класс Index_Basket_Frontend_Controller формирует страницу покупательской корзины,
 * т.е. список товаров в корзине + список рекомендованных товаров, получает данные
 * от модели Basket_Frontend_Model, общедоступная часть сайта
 */
class Index_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * покупательской корзины
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Basket_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Basket_Frontend_Controller
         */
        parent::input();

        // если данные формы были отправлены, т.е. покупатель изменил количество
        // отдельных товарных позиций в корзине и нажал кнопку «Пересчитать»
        if ($this->isPostMethod()) {
            // обращаемся к модели, чтобы обновить корзину
            $this->basketFrontendModel->updateBasket();
            // перенаправляем покупателя опять на страницу корзины
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->basketFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->basketFrontendModel->getURL('frontend/catalog/index')
            ),
        );

        // тип пользователя
        $type = ($this->authUser) ? $this->userFrontendModel->getUserType() : 0;
        
        // получаем от модели список всех корзин
        $allBaskets = $this->basketFrontendModel->getAllBaskets();

        // получаем от модели массив товаров в текущей корзине
        $basketProducts = $this->basketFrontendModel->getBasketProducts();

        // общая стоимость товаров в текущей корзине
        $temp = $this->basketFrontendModel->getTotalCost();
        // общая стоимость товаров в текущей корзине без учета скидки
        $amount = $temp['amount'];
        // общая стоимость товаров в текущей корзине с учетом скидки
        $userAmount = $temp['user_amount'];

        // получаем от модели массив рекомендованных товаров
        $ids = array(); // массив идентификаторов товаров в корзине
        foreach ($basketProducts as $value) {
            $ids[] = $value['id'];
        }
        $recommendedProducts = $this->basketFrontendModel->getRecommendedProducts($ids);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'         => $breadcrumbs,
            // массив всех корзин
            'allBaskets'          => $allBaskets,
            // URL страницы корзины
            'thisPageURL'         => $this->basketFrontendModel->getURL('frontend/basket/index'),
            // атрибут action тега form
            'action'              => $this->basketFrontendModel->getURL('frontend/basket/index'),
            // ссылка для удаления всех товаров из корзины
            'clearBasketURL'      => $this->basketFrontendModel->getURL('frontend/basket/clear'),
            // массив товаров в корзине
            'basketProducts'      => $basketProducts,
            // стоимость товаров в корзине без учета скидки
            'amount'              => $amount,
            // стоимость товаров в корзине с учетом скидки
            'userAmount'          => $userAmount,
            // URL ссылки на страницу оформления заказа
            'checkoutURL'         => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            // массив рекомендованных товаров
            'recommendedProducts' => $recommendedProducts,
            // массив единиц измерения товара
            'units'               => $this->basketFrontendModel->getUnits(),
            // тип пользователя
            'type'                => $type,
        );

    }

}