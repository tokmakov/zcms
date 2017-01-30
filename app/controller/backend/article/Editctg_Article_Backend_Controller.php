<?php
/**
 * Класс Editctg_Article_Backend_Controller для редактирования категории, формирует
 * страницу с формой для редактирования категории, обновляет запись в таблице БД
 * articles_categories, работает с моделью Article_Backend_Model, административная
 * часть сайта
 */
class Editctg_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editctg_Article_Backend_Controller
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
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->articleBackendModel->getURL('backend/article/editctg/id/' . $this->params['id']));
            } else {
                $this->redirect($this->articleBackendModel->getURL('backend/article/allctgs'));
            }
        }

        $this->title = 'Редактирование категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->articleBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleBackendModel->getURL('backend/article/index')
            ),
            array(
                'name' => 'Категории',
                'url'  => $this->articleBackendModel->getURL('backend/article/allctgs')
            ),
        );

        // получаем от модели информацию о категории
        $category = $this->articleBackendModel->getCategory($this->params['id']);
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
            'action'      => $this->articleBackendModel->getURL('backend/article/editctg/id/' . $this->params['id']),
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
        if ($this->issetSessionData('editArticleCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editArticleCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editArticleCategoryForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет категорию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250)); // наименование категории
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // мета-тег description
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
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editArticleCategoryForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор категории

        // обращаемся к модели для обновления категории
        $this->articleBackendModel->updateCategory($data);

        return true;

    }

}