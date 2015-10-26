<?php
/**
 * Класс Addprd_Basket_Frontend_Controller отвечает за добавление товара в корзину,
 * взаимодействует с моделью Basket_Frontend_Model, общедоступная часть сайта
 */
class Addprd_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    /**
     * Функция добавляет товар в корзину и делает редирект на страницу корзины
     * или обратно на страницу, где была нажата кнопка «В корзину»
     */
    protected function input() {

        // данные должны быть отправлены методом POST
        if (!$this->isPostMethod()) {
            $this->notFoundRecord = true;
            return;
        }

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        $count = 1; // кол-во товара
        if (isset($_POST['count']) && ctype_digit($_POST['count'])) {
            $count = $_POST['count'];
        }

        // добавляем товар в корзину
        $this->basketFrontendModel->addToBasket($_POST['product_id'], $count);

        // куда перенаправить посетителя после добавления товара в корзину?
        if (!isset($_POST['return'])) {
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
        }

        $url = 'frontend/basket/index';
        if ($_POST['return'] == 'product') { // перенаправляем на страницу товара
            if (isset($_POST['return_prd_id']) && ctype_digit($_POST['return_prd_id'])) {
                $url = 'frontend/catalog/product/id/' . $_POST['return_prd_id'];
            }
        } elseif ($_POST['return'] == 'category') { // перенаправляем на страницу категории
            if (isset($_POST['return_ctg_id']) && ctype_digit($_POST['return_ctg_id'])) {
                $url = 'frontend/catalog/category/id/' . $_POST['return_ctg_id'];
                if (isset($_POST['group']) && ctype_digit($_POST['group']) && $_POST['group'] > 0) {
                    $url = $url . '/group/' . $_POST['group'];
                }
                if (isset($_POST['maker']) && ctype_digit($_POST['maker']) && $_POST['maker'] > 0) {
                    $url = $url . '/maker/' . $_POST['maker'];
                }
                if (isset($_POST['hit']) && $_POST['hit'] == 1) {
                    $url = $url . '/hit/1';
                }
                if (isset($_POST['new']) && $_POST['new'] == 1) {
                    $url = $url . '/new/1';
                }
                if (isset($_POST['sort']) && ctype_digit($_POST['sort']) && $_POST['sort'] > 0) {
                    $url = $url . '/sort/' . $_POST['sort'];
                }
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'maker') { // перенаправляем на страницу производителя
            if (isset($_POST['return_mkr_id']) && ctype_digit($_POST['return_mkr_id'])) {
                $url = 'frontend/catalog/maker/id/' . $_POST['return_mkr_id'];
                if (isset($_POST['sort']) && ctype_digit($_POST['sort']) && $_POST['sort'] > 0) {
                    $url = $url . '/sort/' . $_POST['sort'];
                }
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'compared') { // перенаправляем на страницу сравнения товаров
            $url = 'frontend/compared/index';
            if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                $url = $url . '/page/' . $_POST['page'];
            }
        } elseif ($_POST['return'] == 'wished') { // перенаправляем на страницу отложенных товаров
            $url = 'frontend/wished/index';
            if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                $url = $url . '/page/' . $_POST['page'];
            }
        } elseif ($_POST['return'] == 'viewed') { // перенаправляем на страницу просмотренных товаров
            $url = 'frontend/viewed/index';
            if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                $url = $url . '/page/' . $_POST['page'];
            }
        } elseif ($_POST['return'] == 'search') { // перенаправляем на страницу результатов поиска по каталогу
            if (!empty($_POST['query'])) {
                $query = rawurlencode(utf8_substr($_POST['query'], 0, 64));
                $url = 'frontend/catalog/search/query/' . $query;
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            } else {
                $url = 'frontend/catalog/search';
            }
        }

        $this->redirect($this->basketFrontendModel->getURL($url));

    }

}