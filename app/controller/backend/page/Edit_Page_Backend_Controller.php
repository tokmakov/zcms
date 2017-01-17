<?php
/**
 * Класс Edit_Page_Backend_Controller формирует страницу с формой для
 * редактирования страницы сайта, обновляет страницу (запись в таблице БД),
 * получает данные от модели Page_Backend_Model
 */
class Edit_Page_Backend_Controller extends Page_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования страницы сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Page_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Edit_Page_Backend_Controller
         */
        parent::input();

        // если не передан id страницы или id страницы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->pageBackendModel->getURL('backend/page/edit/id/' . $this->params['id']));
            } else {
                $this->redirect($this->pageBackendModel->getURL('backend/page/index'));
            }
        }

        $this->title = 'Редактирование страницы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->pageBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->pageBackendModel->getURL('backend/page/index'), 'name' => 'Страницы'),
        );

        // получаем от модели информацию о странице
        $page = $this->pageBackendModel->getPage($this->params['id']);
        // если запрошенная страница не найдена в БД
        if (empty($page)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем информацию о файлах
        $files = array();
        if (is_dir('files/page/' . $this->params['id'])) {
            $temp = scandir('files/page/' . $this->params['id']);
            foreach ($temp as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $files[] = array(
                    'name' => $file,
                    'url'  => $this->config->site->url . 'files/page/' . $this->params['id'] . '/' . $file
                );
            }
        }

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
            'action'      => $this->pageBackendModel->getURL('backend/page/edit/id/' . $this->params['id']),
            // массив страниц для выбора родителя
            'pages'       => $pages,
            // уникальный идентификатор страницы
            'id'          => $this->params['id'],
            // заголовок h1
            'name'        => $page['name'],
            // содержание тега title
            'title'       => $page['title'],
            // ЧПУ (SEF) страницы
            'sefurl'      => $page['sefurl'],
            // мета-тег keywords
            'keywords'    => $page['keywords'],
            // мета-тег description
            'description' => $page['description'],
            // содержание странцы
            'body'        => $page['body'],
            // родитель страницы
            'parent'      => $page['parent'],
            // загруженные файлы
            'files'       => $files,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editPageForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editPageForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editPageForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет страницу и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['sefurl']      = trim(iconv_substr($_POST['sefurl'], 0, 100));      // ЧПУ (SEF) страницы
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250));        // заголовок h1
        $data['title']       = trim(iconv_substr($_POST['title'], 0, 250));       // meta-тег title
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));    // meta-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // meta-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание странцы

        // родитель страницы
        $data['parent'] = $this->pageBackendModel->getPageParent($this->params['id']);
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = (int)$_POST['parent']; // новый родитель
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
        if ($data['parent'] == $this->params['id']) { // родителем страницы назначена она сама?
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }
        // родителем страницы назначен ее потомок?
        if (in_array($data['parent'], $this->pageBackendModel->getAllChildPages($this->params['id']))) {
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее данными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editPageForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор страницы

        // обращаемся к модели для обновления страницы
        $this->pageBackendModel->updatePage($data);

        // загружаем файлы
        $this->pageBackendModel->uploadFiles($this->params['id']);

        return true;

    }

}
