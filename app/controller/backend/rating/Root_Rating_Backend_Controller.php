<?php
/**
 * Класс Root_Rating_Backend_Controller формирует страницу категории верхнего уровня
 * рейтинга продаж, т.е. список дочерних категорий и список товаров рейтинга, получает
 * данные от модели Rating_Backend_Model
 */
class Root_Rating_Backend_Controller extends Rating_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * категории верхнего уровня рейтинга продаж
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Rating_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Root_Rating_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о категории верхнего уровня
        $category = $this->ratingBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'] . '. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->ratingBackendModel->getURL('backend/index/index'),
            ),
            array(
                'name' => 'Рейтинг',
                'url'  => $this->ratingBackendModel->getURL('backend/rating/index'),
            )
        );

        // получаем от модели массив дочерних категорий и товаров рейтинга
        $root = $this->ratingBackendModel->getRootCategory($this->params['id']);

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
            // URL ссылки для добавления товара
            'addPrdUrl'   => $this->ratingBackendModel->getURL('backend/rating/addprd'),
            // массив дочерних категорий и товаров рейтинга
            'root'        => $root,
        );

    }

}
