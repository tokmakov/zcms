<?php
/**
 * Класс Index_Solutions_Backend_Controller формирует сводную страницу со списком
 * всех типовых решений. Получает данные от модели Solutions_Backend_Model,
 * административная часть сайта
 */
class Index_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования сводной
     * страницы типовых решений со списком всех типовых решений
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Solutions_Backend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->solutionsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
        );

        // получаем от модели массив категорий и типовых решений
        $solutions = $this->solutionsBackendModel->getAllSolutions();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // URL сводной страницы типовых решений
            'indexPageUrl'  => $this->solutionsBackendModel->getURL('backend/solutions/index'),
            // URL страницы со списком всех категорий
            'ctgsPageUrl'   => $this->solutionsBackendModel->getURL('backend/solutions/allctgs'),
            // URL страницы с формой для добавления категории
            'addCtgUrl'     => $this->solutionsBackendModel->getURL('backend/solutions/addctg'),
            // URL страницы с формой для добавления типового решения
            'addSltnUrl'    => $this->solutionsBackendModel->getURL('backend/solutions/addsltn'),
            // массив категорий и типовых решений
            'solutions'     => $solutions,
        );

    }

}