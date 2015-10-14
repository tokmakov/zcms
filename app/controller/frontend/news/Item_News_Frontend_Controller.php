<?php
/**
 * Класс Item_News_Frontend_Controller формирует страницу отдельной новости,
 * получает данные от модели News_Frontend_Model
 */
class Item_News_Frontend_Controller extends News_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * отдельной новости
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Item_News_Frontend_Controller
         */
        parent::input();

        // если не передан id новости или id новости не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели данные о новости
        $news = $this->newsFrontendModel->getNewsItem($this->params['id']);
        // если запрошенная новость не найдена в БД
        if (empty($news)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $news['name'] . '. ' . $news['ctg_name'];
        if (!empty($news['keywords'])) {
            $this->keywords = $news['keywords'];
        }
        if (!empty($news['description'])) {
            $this->description = $news['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsFrontendModel->getURL('frontend/news/index'), 'name' => 'Новости'),
            array('url' => $this->newsFrontendModel->getURL('frontend/news/category/id/' . $news['ctg_id']), 'name' => $news['ctg_name']),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'     => $breadcrumbs,
            // заголовок новости
            'name'            => $news['name'],
            // текст новости
            'body'            => $news['body'],
            // дата публикации новости
            'date'            => $news['date'],
            // наименование категории новости
            'categoryName'    => $news['ctg_name'],
            // URL страницы категории новости
            'categoryPageUrl' => $this->newsFrontendModel->getURL('frontend/news/category/id/' . $news['ctg_id'])
        );
    }

}
