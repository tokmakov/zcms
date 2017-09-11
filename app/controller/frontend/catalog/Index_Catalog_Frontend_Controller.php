<?php
/**
 * Класс Index_Catalog_Frontend_Controller формирует главную страницу каталога, т.е.
 * список категорий верхнего уровня + список производителей + список функциональных
 * групп, получает данные от модели Index_Catalog_Frontend_Model, общедоступная часть
 * сайта
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
                'url'  => $this->indexCatalogFrontendModel->getURL('frontend/index/index')
            ),
        );

        // включена сортировка?
        $sort = 0;
        if (isset($_COOKIE['sort']) && in_array($_COOKIE['sort'], array(1,2,3,4,5,6))) {
            $sort = (int)$_COOKIE['sort'];
        }

        // пользователь выбрал кол-во товаров на странице?
        $perpage = 0;
        $others = $this->config->pager->frontend->products->getValue('others'); // доступные варианты
        if (isset($_COOKIE['perpage']) && in_array($_COOKIE['perpage'], $others)) {
            $perpage = (int)$_COOKIE['perpage'];
        }

        // получаем от модели массив категорий верхнего уровня
        $root = $this->indexCatalogFrontendModel->getRootCategories($sort, $perpage);

        // получаем от модели массив производителей
        $makers = $this->makerCatalogFrontendModel->getMakers(20, $sort, $perpage);

        // получаем от модели массив функциональных групп
        $groups = $this->groupCatalogFrontendModel->getGroups(20, $sort, $perpage);

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
            'allMakersURL' => $this->indexCatalogFrontendModel->getURL('frontend/catalog/makers'),
            // URL ссылки на страницу со списком всех функциональных групп
            'allGroupsURL' => $this->indexCatalogFrontendModel->getURL('frontend/catalog/groups'),
        );

    }

}
