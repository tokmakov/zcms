<?php
/**
 * Класс Additem_Menu_Backend_Controller формирует страницу с формой для
 * добавления нового пункта меню, получает данные от модели Menu_Backend_Model,
 * административная часть сайта
 */
class Additem_Menu_Backend_Controller extends Menu_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели Menu_Backend_Model данные, необходимые для
     * формирования страницы с формой для добавления нового пункта меню
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Menu_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Additem_Menu_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ишибок
                $this->redirect($this->menuBackendModel->getURL('backend/menu/additem'));
            } else {
                $this->redirect($this->menuBackendModel->getURL('backend/menu/index'));
            }
        }

        $this->title = 'Новый пункт меню. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->menuBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->menuBackendModel->getURL('backend/menu/index'), 'name' => 'Меню'),
        );

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
            'breadcrumbs'         => $breadcrumbs,
            // атрибут action тега form
            'action'              => $this->menuBackendModel->getURL('backend/menu/additem'),
            // массив всех пунктов меню для возможности выбора родителя
            'menuItems'           => $menuItems,
            // массив всех страниц сайта
            'pages'               => $pages,
            // массив категорий каталога верхнего уровня
            'catalogCategories'   => $catalogCategories,
            // массив категорий блога
            'blogCategories'      => $blogCategories,
            // массив категорий типовых решений
            'solutionCategories'  => $solutionCategories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addMenuItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addMenuItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addMenuItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет пункт меню и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name'] = trim(iconv_substr($_POST['name'], 0, 100)); // наименование пункта меню
        $data['url']  = trim(iconv_substr($_POST['url'], 0, 100));  // URL пункта меню

        // родитель
        $data['parent'] = 0;
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

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addMenuItemForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового пункта меню
        $this->menuBackendModel->addMenuItem($data);

        return true;
    }

}