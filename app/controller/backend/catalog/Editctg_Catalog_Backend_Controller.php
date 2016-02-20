<?php
/**
 * Класс Editctg_Catalog_Backend_Controller для редактирования категории, формирует
 * страницу с формой для редактирования категории, обновляет запись в таблице БД
 * categories, работает с моделью Catalog_Backend_Model, администртивная часть сайта
 */
class Editctg_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного обновления категории и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования категории
     */
    protected function input() {

        // сначала обращаемся к родительскому классу Catalog_Backend_Controller,
        // чтобы получить html-код отдельных частей страницы, которые общие для
        // всех потомков Catalog_Backend_Controller, потом получаем html-код тех
        // частей страницы,  которые нужны только для Editctg_Catalog_Backend_Controller
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
            if ($this->validateForm()) { // ошибок не было, обновление категории прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию отредактированной категории
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу каталога
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/editctg/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // получаем от модели информацию о категории
        $category = $this->catalogBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
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
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/editctg/id/' . $this->params['id']),
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // родительская категория
            'parent'      => $category['parent'],
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
            // наименование категории
            'name'        => $category['name'],
            // мета-тег keywords
            'keywords'    => $category['keywords'],
            // мета-тег description
            'description' => $category['description'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        // и сохраненные данные формы
        if ($this->issetSessionData('editCatalogCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editCatalogCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editCatalogCategoryForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет категорию и возвращает true
     */
    private function validateForm() {

        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250)); // наименование категории
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);

        // родительская категория
        $data['parent'] = $this->catalogBackendModel->getCategoryParent($this->params['id']);
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = $_POST['parent']; // новая родительская категория
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if ($data['parent'] == $this->params['id']) { // родителем категории назначена она сама?
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }
        // родителем категории назначен ее потомок?
        if (in_array($data['parent'], $this->catalogBackendModel->getAllChildIds($this->params['id']))) {
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }

        // были допущены ошибки при заполнении формы, сохраняем введенные
        // пользователем данные, чтобы после редиректа снова показать форму,
        // заполненную введенными ранее данными и сообщением об ошибке
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editCatalogCategoryForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['parent'];

        $data['id'] = $this->params['id']; // уникальный идентификатор категории

        // обращаемся к модели для обновления категории
        $this->catalogBackendModel->updateCategory($data);

        return true;
    }

}