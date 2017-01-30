<?php
/**
 * Класс Edit_Start_Backend_Controller формирует страницу с формой для редактирования
 * витрины (главной страницы сайта), обновляет главную страницу (запись в таблице БД),
 * получает данные от модели Start_Backend_Model
 */
class Edit_Start_Backend_Controller extends Start_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования витрины (главной страницы сайта)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Start_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Edit_Start_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->startBackendModel->getURL('backend/start/edit'));
            } else { // ошибок не было, обновление прошло успешно
                $this->redirect($this->startBackendModel->getURL('backend/start/index'));
            }
        }

        $this->title = 'Витрина. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->startBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->startBackendModel->getURL('backend/start/index'), 'name' => 'Витрина'),
        );

        // получаем информацию о главной странице от модели
        $start = $this->startBackendModel->getStartPage();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->startBackendModel->getURL('backend/start/edit'),
            // заголовок <h1> главной страницы
            'name'        => $start['name'],
            // название главной страницы
            'title'       => $start['title'],
            // содержимое мета-тега keywords
            'keywords'    => $start['keywords'],
            // содержимое мета-тега description
            'description' => $start['description'],
            // текст главной страницы в формате html
            'body'        => $start['body'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editStartPageForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editStartPageForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editStartPageForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет витрину и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250)); // заголовок h1
        $data['title']       = trim(iconv_substr($_POST['title'], 0, 250)); // meta-тег title
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250)); // meta-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // meta-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание странцы

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок страницы»';
        }
        if (empty($data['title'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название страницы»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание страницы»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее данными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editStartPageForm', $data);
            return false;
        }

        // обращаемся к модели для обновления витрины
        $this->startBackendModel->updateStartPage($data);

        return true;

    }

}
