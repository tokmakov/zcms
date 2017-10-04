<?php
/**
 * Класс Add_Page_Backend_Controller формирует страницу с формой для
 * добавления новой страницы, получает данные от модели Page_Backend_Model
 */
class Add_Page_Backend_Controller extends Page_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления новой страницы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Page_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Add_Page_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->pageBackendModel->getURL('backend/page/add'));
            } else {
                $this->redirect($this->pageBackendModel->getURL('backend/page/index'));
            }
        }

        $this->title = 'Новая страница. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->pageBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->pageBackendModel->getURL('backend/page/index'), 'name' => 'Страницы'),
        );

        // получаем от модели массив страниц двух верхних уровней,
        // для возможности выбора родителя
        $pages = $this->pageBackendModel->getPages();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action' => $this->pageBackendModel->getURL('backend/page/add'),
            // массив страниц сайта для возможности выбора родителя
            'pages'  => $pages,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addPageForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addPageForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addPageForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет новую страницу и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['sefurl']      = trim(iconv_substr($_POST['sefurl'], 0, 100));      // ЧПУ (SEF) страницы
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250));        // заголовок h1
        $data['title']       = trim(iconv_substr($_POST['title'], 0, 250));       // содержимое тега title
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));    // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание странцы

        // родитель страницы
        $data['parent'] = 0;
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = $_POST['parent'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок страницы»';
        }
        if (empty($data['title'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название страницы»';
        }
        if (empty($data['sefurl'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «ЧПУ (SEF) страницы»';
        } elseif ( ! preg_match('#^[a-z][-_0-9a-z]#i', $data['sefurl'])) {
            $errorMessage[] = 'Поле «ЧПУ (SEF) страницы» содержит недопустимые символы';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание страницы»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее данными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addPageForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новой страницы
        $pageId = $this->pageBackendModel->addPage($data);

        // загружаем файлы
        $this->pageBackendModel->uploadFiles($pageId);

        return true;

    }

}
