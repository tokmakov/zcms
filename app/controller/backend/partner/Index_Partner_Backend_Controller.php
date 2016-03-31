<?php
/**
 * Класс Index_Partner_Backend_Controller формирует страницу управления партнерами,
 * получает данные от модели Partner_Backend_Model, административная часть сайта
 */
class Index_Partner_Backend_Controller extends Partner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления партнерами
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Partner_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Partner_Backend_Controller
         */
        parent::input();

        $this->title = 'Партнеры. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->partnerBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем массив всех партнеров
        $partners = $this->partnerBackendModel->getAllPartners();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'  => $breadcrumbs,
            // массив всех партнеров
            'partners'      => $partners,
            // URL страницы с формой для добавления партнера
            'addPartnerURL' => $this->partnerBackendModel->getURL('backend/partner/add'),
        );

    }

}