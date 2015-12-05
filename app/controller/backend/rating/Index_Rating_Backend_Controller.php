<?php
/**
 * Класс Index_Rating_Backend_Controller формирует страницу со списком категорий
 * ретинга, получает данные от модели Rating_Backend_Model
 */
class Index_Rating_Backend_Controller extends Rating_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех товаров по сниженным ценам
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Rating_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Rating_Backend_Controller
         */
        parent::input();

        $this->title = 'Рейтинг. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->ratingBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив всех категрий
        $categories = $this->ratingBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех категорий
            'categories'   => $categories,
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->ratingBackendModel->getURL('backend/rating/addctg'),
        );

    }

}