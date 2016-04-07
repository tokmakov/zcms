<?php
/**
 * Класс Index_Solution_Frontend_Controller формирует главную страницу типовых
 * решений, т.е. список категорий + список всех типовых решений, получает данные
 * от модели Solution_Frontend_Model, общедоступная часть сайта
 */
class Index_Solution_Frontend_Controller extends Solution_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования главной
     * страницы типовых решений
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Solution_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->solutionFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
        );

        // получаем от модели массив категорий
        $categories = $this->solutionFrontendModel->getCategories();

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во типовых решений
        $totalSolutions = $this->solutionFrontendModel->getCountAllSolutions();
        // URL этой страницы
        $thisPageURL = $this->solutionFrontendModel->getURL('frontend/solution/index');
        $temp = new Pager(
            $thisPageURL,                                        // URL этой страницы
            $page,                                               // текущая страница
            $totalSolutions,                                     // общее кол-во типовых решений
            $this->config->pager->frontend->solution->perpage,  // типовых решений на страницу
            $this->config->pager->frontend->solution->leftright // кол-во ссылок слева и справа
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
        $start = ($page - 1) * $this->config->pager->frontend->solution->perpage;

        // получаем от модели массив всех типовых решений
        $solutions = $this->solutionFrontendModel->getAllSolutions($start);

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
            'pager' => $pager
        );

    }

}
