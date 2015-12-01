<?php
/**
 * Класс Editctg_News_Backend_Controller для редактирования категории, формирует
 * страницу с формой для редактирования категории, обновляет запись в таблице БД
 * news_ctgs, работает с моделью News_Backend_Model, административная часть сайта
 */
class Editctg_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editctg_News_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->newsBackendModel->getURL('backend/news/editctg/id/' . $this->params['id']));
            } else {
                $this->redirect($this->newsBackendModel->getURL('backend/news/allctgs'));
            }
        }

        $this->title = 'Редактирование категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsBackendModel->getURL('backend/news/index'), 'name' => 'Новости'),
            array('url' => $this->newsBackendModel->getURL('backend/news/allctgs'), 'name' => 'Категории'),
        );

        // получаем от модели информацию о категории
        $category = $this->newsBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->newsBackendModel->getURL('backend/news/editctg/id/' . $this->params['id']),
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории
            'name'        => $category['name'],
            // мета-тег keywords
            'keywords'    => $category['keywords'],
            // мета-тег description
            'description' => $category['description'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editNewsCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editNewsCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editNewsCategoryForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет категорию и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250)); // наименование категории
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editNewsCategoryForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор категории

        // обращаемся к модели для обновления категории
        $this->newsBackendModel->updateCategory($data);

        return true;

    }

}