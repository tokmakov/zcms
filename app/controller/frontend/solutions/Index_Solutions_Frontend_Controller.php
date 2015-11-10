<?php
/**
 * Класс Index_Solutions_Frontend_Controller формирует главную страницу типовых
 * решений, т.е. список категорий + список всех типовых решений, получает данные
 * от модели Solutions_Frontend_Model, общедоступная часть сайта
 */
class Index_Solutions_Frontend_Controller extends Solutions_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования главной
     * страницы типовых решений
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Solutions_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->solutionsFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
        );

        // получаем от модели массив категорий
        $categories = $this->solutionsFrontendModel->getCategories();

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во типовых решений
        $totalSolutions = $this->solutionsFrontendModel->getCountAllSolutions();

        $temp = new Pager(
            $page,                                               // текущая страница
            $totalSolutions,                                     // общее кол-во типовых решений
            $this->config->pager->frontend->solutions->perpage,  // типовых решений на страницу
            $this->config->pager->frontend->solutions->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->solutions->perpage;

        // получаем от модели массив всех типовых решений
        $solutions = $this->solutionsFrontendModel->getAllSolutions($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив категорий
            'categories'  => $categories,
            // массив всех типовых решений
            'solutions'   => $solutions,
            // постраничная навигация
            'pager' => $pager,
            // URL этой страницы
            'thisPageUrl' => $this->solutionsFrontendModel->getURL('frontend/solutions/index'),
        );

    }

}
