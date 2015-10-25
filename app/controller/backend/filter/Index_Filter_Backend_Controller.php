<?php
/**
 * Класс Index_Filter_Backend_Controller формирует сводную страницу со списком
 * функциональных групп, параметров, значений. Получает данные от модели
 * Filter_Backend_Model, административная часть сайта
 */
class Index_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования сводной
     * страницы фильтра товаров со списком групп, параметров, значений
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Filter_Backend_Controller
         */
        parent::input();

        $this->title = 'Фильтр товаров. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->filterBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
        );

        // получаем от модели массив функциональных групп
        $groups = $this->filterBackendModel->getGroups(10);

        // получаем от модели массив параметров подбора
        $params = $this->filterBackendModel->getParams(10);

        // получаем от модели массив значений параметров
        $values = $this->filterBackendModel->getValues(10);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // URL сводной страницы фильтра товаров
            'filterPageUrl'   => $this->filterBackendModel->getURL('backend/filter/index'),
            // URL страницы со списком всех функциональных групп
            'groupsPageUrl' => $this->filterBackendModel->getURL('backend/filter/allgroups'),
            // URL страницы со списком всех параметров подбора
            'paramsPageUrl' => $this->filterBackendModel->getURL('backend/filter/allparams'),
            // URL страницы со списком всех значений параметров
            'valuesPageUrl' => $this->filterBackendModel->getURL('backend/filter/allvalues'),
            // массив функциональных групп
            'groups'        => $groups,
            // массив параметров подбора
            'params'        => $params,
            // массив значений параметров
            'values'        => $values,
        );

    }

}