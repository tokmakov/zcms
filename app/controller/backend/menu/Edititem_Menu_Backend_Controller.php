<?php
/**
 * Класс Edititem_Menu_Backend_Controller формирует страницу с формой для
 * редактирования пункта меню, получает данные от модели Menu_Backend_Model,
 * административная часть сайта
 */
class Edititem_Menu_Backend_Controller extends Menu_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от моделей Menu_Backend_Model, Page_Backend_Model, Catalog_Backend_Model
     * данные, необходимые для формирования страницы с формой для редактирования пункта меню
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Menu_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Edititem_Menu_Backend_Controller
         */
        parent::input();

        // если не передан id пункта меню или id пункта меню не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->menuBackendModel->getURL('backend/menu/edititem/id/' . $this->params['id']));
            } else {
                $this->redirect($this->menuBackendModel->getURL('backend/menu/index'));
            }
        }

        $this->title = 'Редактирование пункт меню. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->menuBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->menuBackendModel->getURL('backend/menu/index'), 'name' => 'Меню'),
        );

        // получаем от модели информацию о пункте меню
        $menuItem = $this->menuBackendModel->getMenuItem($this->params['id']);
        // если запрошенный пункт меню не найден в БД
        if (empty($menuItem)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех пунктов меню для возможности выбора родителя
        $menuItems = $this->menuBackendModel->getMenuItems();

        // получаем от модели массив всех страниц сайта
        $pages = $this->menuBackendModel->getAllPages();

        // получаем от модели массив категорий каталога верхнего уровня
        $catalogCategories = $this->menuBackendModel->getRootCategories();

        // получаем массив всех категорий блога
        $blogCategories = $this->menuBackendModel->getBlogCategories();

        // получаем массив всех категорий типовых решений
        $solutionCategories = $this->menuBackendModel->getSolutionCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'        => $breadcrumbs,
            // атрибут action тега form
            'action'             => $this->menuBackendModel->getURL('backend/menu/edititem/id/' . $this->params['id']),
            // уникальный идентификатор пункта меню
            'id'                 => $this->params['id'],
            // наименование пункта меню
            'name'               => $menuItem['name'],
            // URL пункта меню
            'url'                => $menuItem['url'],
            // родитель пункта меню
            'parent'             => $menuItem['parent'],
            // массив всех пунктов меню для возможности выбора родителя
            'menuItems'          => $menuItems,
            // массив всех страниц сайта
            'pages'              => $pages,
            // массив категорий каталога верхнего уровня
            'catalogCategories'  => $catalogCategories,
            // массив категорий блога
            'blogCategories'     => $blogCategories,
            // массив категорий типовых решений
            'solutionCategories' => $solutionCategories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные данные
        // формы и массив сообщений об ошибках
        if ($this->issetSessionData('editMenuItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editMenuItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editMenuItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет пункт меню и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name'] = trim(iconv_substr($_POST['name'], 0, 100)); // наименование пункта меню
        $data['url']  = trim(iconv_substr($_POST['url'], 0, 100));  // URL пункта меню

        // родитель
        $data['parent'] = $this->menuBackendModel->getMenuItemParent($this->params['id']);
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = $_POST['parent'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['url'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «URL»';
        }
        if ($data['parent'] == $this->params['id']) { // родителем пункта меню назначен он сам?
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }
        // родителем пункта меню назначен его потомок?
        if (in_array($data['parent'], $this->menuBackendModel->getAllChildItems($this->params['id']))) {
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editMenuItemForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор пункта меню

        // обращаемся к модели для обновления записи в таблице БД menu
        $this->menuBackendModel->updateMenuItem($data);

        return true;

    }

}