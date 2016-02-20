<?php
/**
 * Класс Allmkrs_Catalog_Backend_Controller формирует страницу со списком всех производителей,
 * получает данные от модели Catalog_Backend_Model, административная часть сайта
 */
class Allmkrs_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех производителей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Allmkrs_Catalog_Backend_Controller
         */
        parent::input();

        $this->title = 'Все производители. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // получаем от модели информацию о производителях
        $makers = $this->catalogBackendModel->getAllMakers();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // URL ссылки для добавления производителя
            'addMakerUrl' => $this->catalogBackendModel->getURL('backend/catalog/addmkr'),
            // массив всех производителей
            'makers' => $makers,
        );

    }

}
