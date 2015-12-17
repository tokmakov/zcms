<?php
/**
 * Класс Addpost_Blog_Backend_Controller формирует страницу с формой для
 * добавления поста блога, получает данные от модели Blog_Backend_Model
 */
class Addpost_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления поста блога
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addpost_Blog_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->blogBackendModel->getURL('backend/blog/addpost'));
            } else {
                $this->redirect($this->blogBackendModel->getURL('backend/blog/index'));
            }
        }

        $this->title = 'Добавить пост. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->blogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->blogBackendModel->getURL('backend/blog/index'), 'name' => 'Блог'),
        );

        // получаем от модели массив категорий постов, для возможности выбора
        $categories = $this->blogBackendModel->getCategories();

        // получаем от модели массив файлов, которые можно вставить в пост
        $files = $this->blogBackendModel->getFiles();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->blogBackendModel->getURL('backend/blog/addpost'),
            // массив категорий для возможности выбора
            'categories'  => $categories,
            // массив файлов
            'files'       => $files,
            // дата добавления поста
            'date'        => date('d.m.Y'),
            // время добавления поста
            'time'        => date('H:i:s'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addBlogPostForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addBlogPostForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addBlogPostForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет свежую новость и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // заголовок поста
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250));
        // анонс поста
        $data['excerpt']     = trim(utf8_substr($_POST['excerpt'], 0, 1000));
        // мета-тег keywords
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // содержание поста
        $data['body']        = trim($_POST['body']);
        // дата добавления
        $data['date']        = $_POST['date'];
        // время добавления
        $data['time']        = $_POST['time'];

        // категория поста
        $data['category'] = 0;
        // TODO: категорий может быть несколько
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Анонс»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addBlogPostForm', $data);
            return false;
        }

        // обращаемся к модели для добавления поста
        $this->blogBackendModel->addPost($data);

        return true;

    }

}