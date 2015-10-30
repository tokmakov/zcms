<?php
/**
 * Класс Editnews_News_Backend_Controller формирует страницу с формой для
 * редактирования новости, обновляет новость (запись в таблице БД),
 * получает данные от модели News_Backend_Model
 */
class Editnews_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования новости
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editnews_News_Backend_Controller
         */
        parent::input();

        // если не передан id новости или id новости не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->newsBackendModel->getURL('backend/news/editnews/id/' . $this->params['id']));
            } else {
                $this->redirect($this->newsBackendModel->getURL('backend/news/index'));
            }
        }

        $this->title = 'Редактирование новости. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsBackendModel->getURL('backend/news/index'), 'name' => 'Новости'),
        );

        // получаем от модели информацию о новости
        $news = $this->newsBackendModel->getNewsItem($this->params['id']);
        // если запрошенная новость не найдена в БД
        if (empty($news)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем информацию о файлах
        $files = array();
        if (is_dir('./files/news/' . $this->params['id'])) {
            $temp = scandir('./files/news/' . $this->params['id']);
            foreach ($temp as $file) {
                if ($file == '.' || $file == '..' || $file == $this->params['id'] . '.jpg') {
                    continue;
                }
                $files[] = $file;
            }
        }

        // получаем от модели массив категорий новостей, для возможности выбора родителя
        $categories = $this->newsBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->newsBackendModel->getURL('backend/news/editnews/id/' . $this->params['id']),
            // массив категорий новостей
            'categories'  => $categories,
            // уникальный идентификатор новости
            'id'          => $this->params['id'],
            // категория новости
            'category'    => $news['ctg_id'],
            // заголовок новости
            'name'        => $news['name'],
            // мета-тег keywords
            'keywords'    => $news['keywords'],
            // мета-тег description
            'description' => $news['description'],
            // анонс новости
            'excerpt'     => $news['excerpt'],
            // содержание новости
            'body'        => $news['body'],
            // дата добавления
            'date'        => $news['date'],
            // время добавления
            'time'        => $news['time'],
            // загруженные файлы
            'files'       => $files,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editNewsItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editNewsItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editNewsItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет новость и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250)); // заголовок новости
        $data['excerpt']     = trim(utf8_substr($_POST['excerpt'], 0, 1000)); // анонс новости
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание новости
        $data['date']        = $_POST['date']; // дата
        $data['time']        = $_POST['time']; // время

        // категория новости
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = $_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок новости»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Анонс новости»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание новости»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editNewsItemForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор новости

        // обращаемся к модели для обновления новости
        $this->newsBackendModel->updateNewsItem($data);

        return true;

    }

}
