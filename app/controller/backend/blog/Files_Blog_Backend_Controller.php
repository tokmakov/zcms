<?php
/**
 * Класс Files_Blog_Backend_Controller формирует страницу со списком всех
 * файлов блога, получает данные от модели Blog_Backend_Model, административная
 * часть сайта
 */
class Files_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех файлов блога
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Files_Blog_Backend_Controller
         */
        parent::input();
        
        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            // загружаем файл(ы)
            $this->blogBackendModel->uploadFiles();
            // перенаправляем администратора опять на страницу со списком файлов
            $this->redirect($this->blogBackendModel->getURL('backend/blog/files'));
        }

        $this->title = 'Файлы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Блог',
                'url'  => $this->blogBackendModel->getURL('backend/blog/index')
            ),
        );

        // получаем от модели массив массив директорий и файлов
        $folders = $this->blogBackendModel->getFoldersAndFiles(12);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->blogBackendModel->getURL('backend/blog/files'),
            // массив всех файлов
            'folders'      => $folders,
            // URL ссылки на страницу со списком всех постов
            'allPostsUrl' => $this->blogBackendModel->getURL('backend/blog/index'),
            // URL ссылки на страницу со списком всех категорий
            'allCtgsUrl'  => $this->blogBackendModel->getURL('backend/blog/allctgs')
        );

    }

}