<?php
/**
 * Класс Groups_Catalog_Frontend_Controller формирует страницу со списком всех
 * функциональных групп, получает данные от модели Catalog_Frontend_Model,
 * общедоступная часть сайта
 */
class Groups_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех функциональных групп
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Groups_Catalog_Frontend_Controller
         */
        parent::input();
        
        // если данные формы были отправлены: поиск функционала
        if ($this->isPostMethod()) {
            if ( ! empty($_POST['query'])) {
                $_POST['query'] = trim(utf8_substr(str_replace('/', '|', $_POST['query']), 0, 64));
                $this->redirect($this->groupCatalogFrontendModel->getURL('frontend/catalog/groups/query/' . rawurlencode($_POST['query'])));
            } else {
                $this->redirect($this->groupCatalogFrontendModel->getURL('frontend/catalog/groups'));
            }
        }

        $this->title = 'Функциональные группы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->groupCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->groupCatalogFrontendModel->getURL('frontend/catalog/index')
            ),
        );

        // получаем от модели массив результатов поиска
        $result = array();
        if (isset($this->params['query'])) {
            $this->params['query'] = str_replace('|', '/', $this->params['query']);
            $result = $this->groupCatalogFrontendModel->getGroupSearchResult($this->params['query']);
        }

        // получаем от модели массив всех функциональных групп
        $groups = $this->groupCatalogFrontendModel->getAllGroups();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->groupCatalogFrontendModel->getURL('frontend/catalog/groups'),
            // результаты поиска функционала
            'result'      => $result,
            // массив всех функциональных групп
            'groups'      => $groups,
        );

    }

}
