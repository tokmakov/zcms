<?php
/**
 * Класс Category_Solution_Frontend_Controller формирует страницу со списком типовых
 * решений выбранной категории, получает данные от модели Solution_Frontend_Model,
 * общедоступная часть сайта
 */
class Category_Solution_Frontend_Controller extends Solution_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком типовых решений выбранной категории
     */
    protected function input() {

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о категории
        $category = $this->solutionFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * обращаемся к родительскому классу Solution_Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Solution_Frontend_Controller
         */
        parent::input();

        $this->title = $category['name'];
        if ( ! empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if ( ! empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionFrontendModel->getURL('frontend/solution/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во типовых решений в выбранной категории
        $totalSolutions = $this->solutionFrontendModel->getCountCategorySolutions($this->params['id']);
        // URL этой страницы
        $thisPageURL =
            $this->solutionFrontendModel->getURL('frontend/solution/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalSolutions,                                    // общее кол-во типовых решений в категории
            $this->config->pager->frontend->solution->perpage,  // типовых решений на страницу
            $this->config->pager->frontend->solution->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->solution->perpage;

        // получаем от модели массив всех типовых решений
        $solutions = $this->solutionFrontendModel->getCategorySolutions($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории
            'name'        => $category['name'],
            // массив типовых решений выбранной категории
            'solutions'   => $solutions,
            // постраничная навигация
            'pager'       => $pager
        );

    }

}
