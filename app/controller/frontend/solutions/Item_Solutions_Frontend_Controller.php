<?php
/**
 * Класс Item_Solutions_Frontend_Controller формирует страницу типового решения,
 * получает данные от модели Solutions_Frontend_Model, общедоступная часть сайта
 */
class Item_Solutions_Frontend_Controller extends Solutions_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * типового решения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Item_Solutions_Frontend_Controller
         */
        parent::input();

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о типовом решении
        $solution = $this->solutionsFrontendModel->getSolution($this->params['id']);
        // если запрошенное типовое решение не найдено в БД
        if (empty($solution)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $solution['name'];
        if (!empty($solution['keywords'])) {
            $this->keywords = $solution['keywords'];
        }
        if (!empty($solution['description'])) {
            $this->description = $solution['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->solutionsFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url' => $this->solutionsFrontendModel->getURL('frontend/solutions/index')
            ),
            array(
                'name' => $solution['ctg_name'],
                'url' => $this->solutionsFrontendModel->getURL('frontend/solutions/category/id/' . $solution['ctg_id'])
            ),
        );

        // получаем от модели массив товаров типового решения
        $products = $this->solutionsFrontendModel->getSolutionProducts($this->params['id']);

        // единицы измерения
        $units = $this->solutionsFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор типового решения
            'id'          => $this->params['id'],
            // наименование типового решения
            'name'        => $solution['name'],
            // массив товаров типового решения
            'products'    => $products,
            // единицы измерения
            'units'       => $units,
            // основное содержание типового решения
            'content1'    => $solution['content1'],
            // дополнительное содержание типового решения (заключение)
            'content2'    => $solution['content2'],
            // атрибут action тега form для добавления товаров в корзину
            'action'      => $this->solutionsFrontendModel->getURL('frontend/solutions/basket/id/' . $this->params['id']),
        );

    }

}
