<?php
/**
 * Класс Allvalues_Filter_Backend_Controller формирует страницу со списком
 * значений параметров подбора, получает данные от модели Filter_Backend_Model,
 * административная часть сайта
 */
class Allvalues_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех значений параметров подбора
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allvalues_Filter_Backend_Controller
         */
        parent::input();

        $this->title = 'Значения. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->filterBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->filterBackendModel->getURL('backend/filter/index'), 'name' => 'Фильтр'),
        );

        // получаем от модели массив всех значений параметров подбора
        $values = $this->filterBackendModel->getAllValues();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // URL сводной страницы фильтра товаров
            'filterPageUrl' => $this->filterBackendModel->getURL('backend/filter/index'),
            // URL страницы со списком всех функциональных групп
            'groupsPageUrl' => $this->filterBackendModel->getURL('backend/filter/allgroups'),
            // URL страницы со списком всех параметров подбора
            'paramsPageUrl' => $this->filterBackendModel->getURL('backend/filter/allparams'),
            // URL страницы со списком всех значений параметров
            'valuesPageUrl' => $this->filterBackendModel->getURL('backend/filter/allvalues'),
            // URL страницы с формой для добавления нового значения параметра
            'addValueUrl'   => $this->filterBackendModel->getURL('backend/filter/addvalue'),
            // массив значений параметров подбора
            'values'        => $values,
        );

    }

}