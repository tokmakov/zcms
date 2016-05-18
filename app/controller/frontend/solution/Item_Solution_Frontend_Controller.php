<?php
/**
 * Класс Item_Solution_Frontend_Controller формирует страницу типового решения,
 * получает данные от модели Solution_Frontend_Model, общедоступная часть сайта
 */
class Item_Solution_Frontend_Controller extends Solution_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * типового решения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Item_Solution_Frontend_Controller
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
        $solution = $this->solutionFrontendModel->getSolution($this->params['id']);
        // если запрошенное типовое решение не найдено в БД
        if (empty($solution)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $solution['name'];
        if ( ! empty($solution['keywords'])) {
            $this->keywords = $solution['keywords'];
        }
        if ( ! empty($solution['description'])) {
            $this->description = $solution['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->solutionFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url' => $this->solutionFrontendModel->getURL('frontend/solution/index')
            ),
            array(
                'name' => $solution['ctg_name'],
                'url' => $this->solutionFrontendModel->getURL('frontend/solution/category/id/' . $solution['ctg_id'])
            ),
        );

        // получаем от модели массив товаров типового решения
        $complect = $this->solutionFrontendModel->getSolutionProducts($this->params['id']);

        // единицы измерения
        $units = $this->solutionFrontendModel->getUnits();

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
            // URL ссылки для скачивания PDF-файла
            'pdfURL'      => $solution['url']['pdf'],
            // URL ссылки на файл изображения
            'imgURL'      => $solution['url']['img'],
            // массив товаров типового решения
            'complect'    => $complect,
            // единицы измерения
            'units'       => $units,
            // основное содержание типового решения
            'content1'    => $solution['content1'],
            // дополнительное содержание типового решения (заключение)
            'content2'    => $solution['content2'],
            // атрибут action тега form для добавления товаров в корзину
            'action'      => $this->solutionFrontendModel->getURL('frontend/solution/basket/id/' . $this->params['id']),
        );

    }

}
