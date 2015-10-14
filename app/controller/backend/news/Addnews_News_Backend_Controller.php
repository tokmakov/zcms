<?php
/**
 * Класс Addnews_News_Backend_Controller формирует страницу с формой для
 * добавления новости, получает данные от модели News_Backend_Model
 */
class Addnews_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        // сначала обращаемся к родительскому классу News_Backend_Controller,
        // чтобы установить значения переменных, которые нужны для работы всех его
        // потомков, потом переопределяем эти переменные (если необходимо) и
        // устанавливаем значения перменных, которые нужны для работы только
        // Addnews_News_Backend_Controller
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->newsBackendModel->getURL('backend/news/addnews'));
            } else {
                $this->redirect($this->newsBackendModel->getURL('backend/news/index'));
            }
        }

        $this->title = 'Добавить новость. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsBackendModel->getURL('backend/news/index'), 'name' => 'Новости'),
        );

        // получаем от модели массив категорий новостей, для возможности выбора
        $categories = $this->newsBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->newsBackendModel->getURL('backend/news/addnews'),
            // массив категорий для возможности выбора
            'categories'  => $categories,
            // дата добавления новости
            'date'        => date('d.m.Y'),
            // время добавления новости
            'time'        => date('H:i:s'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addNewsItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addNewsItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addNewsItemForm');
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
            $this->setSessionData('addNewsItemForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новости
        $this->newsBackendModel->addNewsItem($data);

        return true;

    }

}