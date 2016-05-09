<?php
/**
 * Класс Show_Solution_Backend_Controller формирует страницу со списком товаров
 * выбранного типового решения. Получает данные от модели Solution_Backend_Model,
 * административная часть сайта
 */
class Show_Solution_Backend_Controller extends Solution_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком товаров выбранного типового решения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Show_Solution_Backend_Controller
         */
        parent::input();

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о типовом решении
        $name = $this->solutionBackendModel->getSolutionName($this->params['id']);
        // если запрошенное типовое решение не найдено в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $name . '. ' . $this->title;

        // получаем от модели идентификатор и наименование категории типового решения
        $category = $this->solutionBackendModel->getSolutionCategory($this->params['id']);
        $categoryName = $this->solutionBackendModel->getCategoryName($category);
        
        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionBackendModel->getURL('backend/solution/index')
            ),
            array(
                'name' => 'Категории',
                'url'  => $this->solutionBackendModel->getURL('backend/solution/allctgs')
            ),
            array(
                'name' => $categoryName,
                'url'  => $this->solutionBackendModel->getURL('backend/solution/category/id/' . $category)
            ),
        );

        // получаем от модели массив товаров выбранного типового решения
        $products = $this->solutionBackendModel->getSolutionProducts($this->params['id']);

        // единицы измерения
        $units = $this->solutionBackendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор типового решения
            'id'          => $this->params['id'],
            // наименование типового решения
            'name'        => $name,
            // массив товаров выбранного типового решения
            'products'    => $products,
            // единицы измерения
            'units'       => $units,
            // URL страницы с формой для добавления товара
            'addPrdUrl'   => $this->solutionBackendModel->getURL('backend/solution/addprd/parent/' . $this->params['id']),
        );

    }

}