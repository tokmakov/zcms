<?php
/**
 * Класс Additem_Article_Backend_Controller формирует страницу с формой для
 * добавления статьи, получает данные от модели Article_Backend_Model
 */
class Additem_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления статьи
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Additem_Article_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->articleBackendModel->getURL('backend/article/additem'));
            } else {
                $this->redirect($this->articleBackendModel->getURL('backend/article/index'));
            }
        }

        $this->title = 'Новая статья. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->articleBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleBackendModel->getURL('backend/article/index')
            ),
        );

        // получаем от модели массив категорий статей, для возможности выбора
        $categories = $this->articleBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->articleBackendModel->getURL('backend/article/additem'),
            // массив категорий для возможности выбора
            'categories'  => $categories,
            // дата добавления статьи
            'date'        => date('d.m.Y'),
            // время добавления статьи
            'time'        => date('H:i:s'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addArticleForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addArticleForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addArticleForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет свежую статью и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250)); // заголовок статьи
        $data['excerpt']     = trim(iconv_substr($_POST['excerpt'], 0, 1000)); // анонс статьи
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание статьи
        $data['date']        = $_POST['date']; // дата публикации
        $data['time']        = $_POST['time']; // время публикации

        // категория статьи
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок статьи»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Анонс статьи»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание статьи»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addArticleForm', $data);
            return false;
        }

        // обращаемся к модели для добавления статьи
        $this->articleBackendModel->addArticle($data);

        return true;

    }

}