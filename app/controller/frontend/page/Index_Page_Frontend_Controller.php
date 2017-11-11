<?php
/**
 * Класс Index_Page_Frontend_Controller формирует отдельную страницу сайта, получает
 * данные от модели Page_Frontend_Model, общедоступная часть сайта
 */
class Index_Page_Frontend_Controller extends Page_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        // если не передан id страницы или id страницы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о странице
        $page = $this->pageFrontendModel->getPage($this->params['id']);
        // если запрошенная страница не найдена в БД
        if (empty($page)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Page_Frontend_Controller
         */
        parent::input();

        /*
         * заголовок страницы (тег <title>), мета-теги keywords и description
         */
        $this->title = $page['title'];
        if ( ! empty($page['keywords'])) {
            $this->keywords    = $page['keywords'];
        }
        if ( ! empty($page['description'])) {
            $this->description = $page['description'];
        }

        // получаем от модели хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/page/index/id/' . $this->params['id']);

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,  // хлебные крошки
            'name'        => $page['name'], // заголовок h1 страницы
            'body'        => $page['body'], // содержимое страницы
        );

    }

}