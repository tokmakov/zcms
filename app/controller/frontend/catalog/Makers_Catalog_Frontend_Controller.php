<?php
/**
 * Класс Makers_Catalog_Frontend_Controller формирует страницу со списком всех
 * производителей, получает данные от модели Maker_Catalog_Frontend_Model,
 * общедоступная часть сайта
 */
class Makers_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех производителей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Makers_Catalog_Frontend_Controller
         */
        parent::input();

        // если данные формы были отправлены: поиск производителя
        if ($this->isPostMethod()) {
            if ( ! empty($_POST['query'])) {
                $_POST['query'] = trim(utf8_substr(str_replace('/', '|', $_POST['query']), 0, 64));
                $this->redirect($this->makerCatalogFrontendModel->getURL('frontend/catalog/makers/query/' . rawurlencode($_POST['query'])));
            } else {
                $this->redirect($this->makerCatalogFrontendModel->getURL('frontend/catalog/makers'));
            }
        }

        $this->title = 'Производители. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->makerCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->makerCatalogFrontendModel->getURL('frontend/catalog/index')
            ),
        );

        // получаем от модели массив результатов поиска
        $result = array();
        if (isset($this->params['query'])) {
            $this->params['query'] = str_replace('|', '/', $this->params['query']);
            $result = $this->makerCatalogFrontendModel->getMakerSearchResult($this->params['query']);
        }

        // получаем от модели массив всех производителей
        $makers = $this->makerCatalogFrontendModel->getAllMakers();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->makerCatalogFrontendModel->getURL('frontend/catalog/makers'),
            // результаты поиска производителя
            'result'      => $result,
            // массив всех производителей
            'makers'      => $makers,
        );

    }

}