<?php
/**
 * Класс Addctg_Catalog_Backend_Controller для добавления новой категории каталога,
 * формирует страницу с формой для добавления категории, добавляет запись в таблицу БД
 * categories, работает с моделью Catalog_Backend_Model, административная часть сайта
 */
class Addctg_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного добавления категории и редиректа
     */
    protected $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addctg_Catalog_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление категории прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию добавленной категории
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу каталога
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/addctg'));
            }
        }

        $this->title = 'Новая категория. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // если передан параметр parent, это родительская категория по умолчанию
        $parent = 0;
        if (isset($this->params['parent']) && ctype_digit($this->params['parent'])) {
            $parent = $this->params['parent'];
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->catalogBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/addctg'),
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
            // родительская категория по умолчанию
            'parent'      => $parent,
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('addCatalogCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addCatalogCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addCatalogCategoryForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет категорию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование категории
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250));
        // мета-тег keywords
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);

        // родительская категория
        $data['parent'] = 0;
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = $_POST['parent'];
        }

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
            $this->setSessionData('addCatalogCategoryForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['parent'];

        // обращаемся к модели для добавления новой категории
        $this->catalogBackendModel->addCategory($data);

        return true;
    }

}