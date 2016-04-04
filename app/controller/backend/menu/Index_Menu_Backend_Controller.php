<?php
/**
 * Класс Index_Menu_Backend_Controller формирует страницу со списком всех пунктов
 * меню сайта, получает данные от модели Menu_Backend_Model, административная
 * часть сайта
 */
class Index_Menu_Backend_Controller extends Menu_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Menu_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Menu_Backend_Controller
         */
        parent::input();

        $this->title = 'Главное меню. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->menuBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив всех пунктов меню
        $menuItems = $this->menuBackendModel->getAllMenuItems();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // URL ссылки на страницу с формой для добавления пункта меню
            'addItemUrl'  => $this->menuBackendModel->getURL('backend/menu/additem'),
            // массив всех пунктов меню
            'menuItems'   => $menuItems,
        );

    }

}