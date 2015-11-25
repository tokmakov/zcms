<?php
/**
 * Класс Category_Solutions_Frontend_Controller формирует страницу со списком типовых
 * решений выбранной категории, получает данные от модели Solutions_Frontend_Model,
 * общедоступная часть сайта
 */
class Category_Solutions_Frontend_Controller extends Solutions_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком типовых решений выбранной категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Solutions_Frontend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о категории
        $category = $this->solutionsFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'];
        if (!empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if (!empty($category['description'])) {
            $this->description = $category['description'];
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
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во типовых решений в выбранной категории
        $totalSolutions = $this->solutionsFrontendModel->getCountCategorySolutions($this->params['id']);
        // URL этой страницы
        $thisPageURL =
            $this->solutionsFrontendModel->getURL('frontend/solutions/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageURL,                                        // URL этой страницы
            $page,                                               // текущая страница
            $totalSolutions,                                     // общее кол-во типовых решений в категории
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
        $solutions = $this->solutionsFrontendModel->getCategorySolutions($this->params['id'], $start);

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
