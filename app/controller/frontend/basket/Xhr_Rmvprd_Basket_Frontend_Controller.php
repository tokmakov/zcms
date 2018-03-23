<?php
/**
 * Класс Xhr_Rmvprd_Basket_Frontend_Controller принимает запрос XmlHttpRequest, удаляет
 * товар из корзины (на странице корзины), работает с моделью Basket_Frontend_Model,
 * общедоступная часть сайта. Возвращает результат в формате JSON, три фрагмента html-кода
 * 1. HTML корзины в правой колонке
 * 2. HTML корзины в центральной колонке
 * 3. HTML списка рекомендованных товаров
 */
class Xhr_Rmvprd_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // не использовать кэширование шаблонов
        $this->notUseCache = true;
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // удаляем товар из корзины
        $this->basketFrontendModel->removeFromBasket($this->params['id']);

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине (для правой колонки)
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // тип пользователя
        $type = ($this->authUser) ? $this->userFrontendModel->getUserType() : 0;

        // получаем от модели массив товаров в корзине (для центральной колонки)
        $basketProducts = $this->basketFrontendModel->getBasketProducts();

        // общая стоимость товаров в корзине (для центральной колонки)
        $temp = $this->basketFrontendModel->getTotalCost();
        // общая стоимость товаров в корзине без учета скидки
        $amount = $temp['amount'];
        // общая стоимость товаров в корзине с учетом скидки
        $userAmount = $temp['user_amount'];

        // получаем от модели массив рекомендованных товаров
        $ids = array(); // массив идентификаторов товаров в корзине
        foreach ($basketProducts as $value) {
            $ids[] = $value['id'];
        }
        $recommendedProducts = $this->basketFrontendModel->getRecommendedProducts($ids);

        /*
         * Получаем три фрагмента html-кода, разделенные символом ¤:
         * 1. Таблица товаров в корзине, правая колонка
         * 2. Таблица товаров в корзине, центральная колонка
         * 3. Список рекомендованных товаров (с этими товарами покупают)
         */
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/basket/xhr/basket.php',
            array(
                // массив товаров в корзине (для правой колонки)
                'sideBasketProducts'  => $sideBasketProducts,
                // общая стоимость товаров в корзине (для правой колонки)
                'sideBasketTotalCost' => $sideBasketTotalCost,
                // URL страницы покупательской корзины
                'thisPageURL'         => $this->basketFrontendModel->getURL('frontend/basket/index'),
                // URL страницы оформления заказа
                'checkoutURL'         => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
                // атрибут action тега form
                'action'              => $this->basketFrontendModel->getURL('frontend/basket/index'),
                // ссылка для удаления всех товаров из корзины
                'clearBasketURL'      => $this->basketFrontendModel->getURL('frontend/basket/clear'),
                // массив товаров в корзине (для центральной колонки)
                'basketProducts'      => $basketProducts,
                // стоимость товаров в корзине без учета скидки (для центральной колонки)
                'amount'              => $amount,
                // стоимость товаров в корзине с учетом скидки (для центральной колонки)
                'userAmount'          => $userAmount,
                // массив единиц измерения товара
                'units'               => $this->basketFrontendModel->getUnits(),
                // количество позиций в корзине
                'count'               => $this->basketFrontendModel->getBasketCount(),
                // тип пользователя
                'type'                => $type,
                // массив рекомендованных товаров
                'recommendedProducts' => $recommendedProducts,
            )
        );
        // разделяем три фрагмента html-кода по символу ¤
        $output = explode('¤', $output);
        $result = array(
            'side' => $output[0],
            'center' => $output[1],
            'upsell' => $output[2],
            'count' => $output[3]
        );
        // преобразуем массив в формат JSON
        $this->output = json_encode($result);
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