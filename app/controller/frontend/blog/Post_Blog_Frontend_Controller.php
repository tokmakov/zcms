<?php
/**
 * Класс Post_Blog_Frontend_Controller формирует страницу отдельной записи (поста)
 * блога, получает данные от модели Blog_Frontend_Model, общедоступная часть сайта
 */
class Post_Blog_Frontend_Controller extends Blog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * отдельной записи (поста) блога
     */
    protected function input() {

        // если не передан id поста блога или id поста блога не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о записи блога
        $post = $this->blogFrontendModel->getPost($this->params['id']);
        // если запрошенная запись не найдена в БД
        if (empty($post)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * сначала обращаемся к родительскому классу Blog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Post_Blog_Frontend_Controller
         */
        parent::input();

        /*
         * заголовок страницы (тег <title>), мета-теги keywords и description
         */
        $this->title = $post['name'] . '. ' . $post['ctg_name'];
        if ( ! empty($post['keywords'])) {
            $this->keywords = $post['keywords'];
        }
        if ( ! empty($post['description'])) {
            $this->description = $post['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Новости',
                'url'  => $this->blogFrontendModel->getURL('frontend/blog/index')
            ),
            array(
                'name' => $post['ctg_name'],
                'url'  => $this->blogFrontendModel->getURL('frontend/blog/category/id/' . $post['ctg_id'])
            ),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'     => $breadcrumbs,
            // заголовок поста блога
            'name'            => $post['name'],
            // текст поста блога
            'body'            => $post['body'],
            // дата публикации
            'date'            => $post['date'],
            // наименование категории
            'categoryName'    => $post['ctg_name'],
            // URL страницы категории
            'categoryPageUrl' =>
                $this->blogFrontendModel->getURL('frontend/blog/category/id/' . $post['ctg_id'])
        );
    }

}
