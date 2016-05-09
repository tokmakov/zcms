<?php
/**
 * Класс Allctgs_Solution_Backend_Controller формирует страницу со списком
 * категорий. Получает данные от модели Solution_Backend_Model, административная
 * часть сайта
 */
class Allctgs_Solution_Backend_Controller extends Solution_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех категорий типовых решений
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allctgs_Solution_Backend_Controller
         */
        parent::input();

        $this->title = 'Категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->solutionBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->solutionBackendModel->getURL('backend/solution/index'), 'name' => 'Типовые решения'),
        );

        // получаем от модели массив категорий
        $categories = $this->solutionBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // URL сводной страницы типовых решений
            'indexPageUrl'  => $this->solutionBackendModel->getURL('backend/solution/index'),
            // URL страницы со списком всех категорий
            'ctgsPageUrl'   => $this->solutionBackendModel->getURL('backend/solution/allctgs'),
            // URL страницы с формой для добавления категории
            'addCtgUrl'     => $this->solutionBackendModel->getURL('backend/solution/addctg'),
            // URL страницы с формой для добавления типового решения
            'addSltnUrl'    => $this->solutionBackendModel->getURL('backend/solution/addsltn'),
            // массив категорий
            'categories'    => $categories,
        );

    }

}