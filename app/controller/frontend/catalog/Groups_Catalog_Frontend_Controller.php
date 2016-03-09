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
        
        // если данные формы были отправлены: поиск по функционалу
        if ($this->isPostMethod()) {
            if ( ! empty($_POST['query'])) {
                $_POST['query'] = trim(utf8_substr($_POST['query'], 0, 64));
                $this->redirect($this->catalogFrontendModel->getURL('frontend/catalog/groups/query/' . rawurlencode($_POST['query'])));
            } else {
                $this->redirect($this->catalogFrontendModel->getURL('frontend/catalog/groups'));
            }
        }

        $this->title = 'Функциональные группы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->catalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url' => $this->catalogFrontendModel->getURL('frontend/catalog/index')
            ),
        );
        
        // получаем от модели массив результатов поиска
        $result = array();
        if (isset($this->params['query'])) {
            $result = $this->catalogFrontendModel->getGroupSearchResult($this->params['query']);
        }

        // получаем от модели массив всех функциональных групп
        $groups = $this->catalogFrontendModel->getAllGroups();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs, // хлебные крошки
            'result'      => $result,      // результаты поиска по функционалу
            'groups'      => $groups,      // массив всех функциональных групп
        );

    }

}
