<?php
/**
 * Класс Editpost_Blog_Backend_Controller формирует страницу с формой для
 * редактирования поста, обновляет запись в таблице БД blog_posts, получает
 * данные от модели Blog_Backend_Model, административная часть сайта
 */
class Editpost_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования поста
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Идщп_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editpost_Blog_Backend_Controller
         */
        parent::input();

        // если не передан id поста или id поста не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->blogBackendModel->getURL('backend/blog/editpost/id/' . $this->params['id']));
            } else {
                $this->redirect($this->blogBackendModel->getURL('backend/blog/index'));
            }
        }

        $this->title = 'Редактирование поста. ' . $this->title;

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

        // получаем от модели информацию о посте
        $post = $this->blogBackendModel->getPost($this->params['id']);
        // если запрошенный пост не найден в БД
        if (empty($post)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив категорий, для возможности выбора
        $categories = $this->blogBackendModel->getCategories();
        
        // получаем от модели массив массив директорий и файлов
        $folders = $this->blogBackendModel->getFoldersAndFiles();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->blogBackendModel->getURL('backend/blog/editpost/id/' . $this->params['id']),
            // массив категорий новостей
            'categories'  => $categories,
            // массив директорий и файлов
            'folders'     => $folders,
            // уникальный идентификатор новости
            'id'          => $this->params['id'],
            // категория новости
            'category'    => $post['ctg_id'],
            // заголовок новости
            'name'        => $post['name'],
            // мета-тег keywords
            'keywords'    => $post['keywords'],
            // мета-тег description
            'description' => $post['description'],
            // анонс новости
            'excerpt'     => $post['excerpt'],
            // содержание поста
            'body'        => $post['body'],
            // дата добавления
            'date'        => $post['date'],
            // время добавления
            'time'        => $post['time'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editBlogPostForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editBlogPostForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editBlogPostForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция обновляет
     * запись (пост) блога и возвращает true
     */
    private function validateForm() {

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
        // дата
        $data['date']        = $_POST['date'];
        // время
        $data['time']        = $_POST['time'];

        // категория поста
        $data['category'] = 0;
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
            $this->setSessionData('editBlogPostForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор поста

        // обращаемся к модели для обновления поста
        $this->blogBackendModel->updatePost($data);

        return true;

    }

}
