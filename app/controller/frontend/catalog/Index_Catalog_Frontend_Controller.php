<?php
/**
 * Класс Index_Catalog_Frontend_Controller формирует главную страницу каталога,
 * т.е. список категорий верхнего уровня + список производителей, получает данные
 * от модели Catalog_Frontend_Model, общедоступная часть сайта
 */
class Index_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования главной
     * страницы каталога
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Catalog_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->catalogFrontendModel->getURL('frontend/index/index')
            ),
        );

        // получаем от модели массив категорий верхнего уровня
        $root = $this->catalogFrontendModel->getRootCategories();

        // получаем от модели массив производителей
        $makers = $this->catalogFrontendModel->getMakers(20);
        
        // получаем от модели массив функциональных групп
        $groups = $this->catalogFrontendModel->getGroups(20);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'  => $breadcrumbs,
            // массив категорий верхнего уровня
            'root'         => $root,
            // массив производителей
            'makers'       => $makers,
            // массив функциональных групп
            'groups'       => $groups,
            // URL ссылки на страницу со списком всех производителей
            'allMakersURL' => $this->catalogFrontendModel->getURL('frontend/catalog/allmkrs'),
            // URL ссылки на страницу со списком всех функциональных групп
            'allGroupsURL' => $this->catalogFrontendModel->getURL('frontend/catalog/groups'),
        );

    }

}
