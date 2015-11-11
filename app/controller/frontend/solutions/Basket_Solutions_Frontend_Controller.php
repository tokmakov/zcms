<?php
/**
 * Класс Basket_Solutions_Frontend_Controller отвечает за добавление в корзину всех
 * товаров типового решения, взаимодействует с моделью Solutions_Frontend_Model,
 * общедоступная часть сайта
 */
class Basket_Solutions_Frontend_Controller extends Solutions_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, нужно только получить
     * список товаров типового решения и добавить их все в корзину. И сделать
     * редирект обратно на страницу типового решения.
     */
    protected function input() {

        // данные должны быть отправлены методом POST
        if ( ! $this->isPostMethod()) {
            $this->notFoundRecord = true;
            return;
        }

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // добавляем товары в корзину
        $this->solutionsFrontendModel->AddSolutionToBasket($this->params['id']);

        // редирект на страницу типового решения
        $url = 'frontend/solutions/item/id/' . $this->params['id'];
        $this->redirect($this->solutionsFrontendModel->getURL($url));

    }
}