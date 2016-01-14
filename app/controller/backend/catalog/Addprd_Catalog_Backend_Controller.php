<?php
/**
 * Класс Addprd_Catalog_Backend_Controller для добавления нового товара, формирует
 * страницу с формой для добавления товара, добавляет новую запись в таблицу БД
 * products, работает с моделью Catalog_Backend_Model
 */
class Addprd_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного добавления товара и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addprd_Catalog_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление товара прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию добавленного товара
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу каталога
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/addprd'));
            }
        }

        $this->title = 'Новый товар. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // если передан параметр category, это родительская категория по умолчанию
        $category = 0;
        if (isset($this->params['category']) && ctype_digit($this->params['category'])) {
            $category = (int)$this->params['category'];
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->catalogBackendModel->getAllCategories();

        // получаем от модели массив всех функиональных групп, для возможности выбора
        $groups = $this->filterBackendModel->getGroups();

        // получаем от модели массив всех производителей, для возможности выбора
        $makers = $this->catalogBackendModel->getMakers();

        // единицы измерения для возможности выбора
        $units = $this->catalogBackendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/addprd'),
            // родительская категория по умолчанию
            'category'    => $category,
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
            // массив всех функциональных групп, для возможности выбора
            'groups'      => $groups,
            // массив всех производителей, для возможности выбора
            'makers'      => $makers,
            // все единицы измерения для возможности выбора
            'units'       => $units,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addCatalogProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addCatalogProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addCatalogProductForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет новый товар и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // наименование изделия
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250));
        // функциональное наименование изделия
        $data['title']       = trim(utf8_substr($_POST['title'], 0, 250));
        // мета-тег keywords
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // код (артикул) товара
        $data['code']        = trim(utf8_substr($_POST['code'], 0, 16));
        // краткое описание
        $data['shortdescr']  = trim(utf8_substr($_POST['shortdescr'], 0, 2000));
        // назначение изделия
        $data['purpose']     = trim($_POST['purpose']);
        // особенности изделия
        $data['features']    = trim($_POST['features']);
        // комплектация изделия
        $data['complect']    = trim($_POST['complect']);
        // дополнительное оборудование
        $data['equipment']   = trim($_POST['equipment']);
        // дополнительно
        $data['padding']     = trim($_POST['padding']);

        // технические характеристики
        $data['techdata'] = '';
        $techdataName = array();
        foreach ($_POST['techdata_name'] as $name) {
            $tmp = trim(utf8_substr($name, 0, 250));
            $tmp = preg_replace('~\s+~', ' ', $tmp);
            $techdataName[] = $tmp;
        }
        $techdataValue = array();
        foreach ($_POST['techdata_value'] as $value) {
            $tmp = trim(utf8_substr($value, 0, 250));
            $tmp = preg_replace('~\s+~', ' ', $tmp);
            $techdataValue[] = $tmp;
        }
        $techdataNameValue = array();
        foreach ($techdataName as $key => $name) {
            if (empty($name) && empty ($techdataValue[$key])) {
                continue;
            }
            $techdataNameValue[] = array($name, $techdataValue[$key]);
        }
        if (count($techdataNameValue) > 0) {
            $data['techdata'] = serialize($techdataNameValue);
        }

        // цена
        $data['price'] = 0.0;
        $temp = trim($_POST['price']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price'] = (float)$temp;
        }
        $data['price2'] = $data['price'];
        $temp = trim($_POST['price2']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price2'] = (float)$temp;
        }
        $data['price3'] = $data['price'];
        $temp = trim($_POST['price3']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price3'] = (float)$temp;
        }
        $data['price4'] = $data['price'];
        $temp = trim($_POST['price4']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price4'] = (float)$temp;
        }
        $data['price5'] = $data['price'];
        $temp = trim($_POST['price5']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price5'] = (float)$temp;
        }
        $data['price6'] = $data['price'];
        $temp = trim($_POST['price6']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price6'] = (float)$temp;
        }
        $data['price7'] = $data['price'];
        $temp = trim($_POST['price7']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price7'] = (float)$temp;
        }

        // единица измерения
        $data['unit'] = 0;
        if (ctype_digit($_POST['unit'])) {
            $data['unit'] = (int)$_POST['unit'];
        }

        // родительская категория
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // дополнительная категория
        $data['category2'] = 0;
        if (ctype_digit($_POST['category2'])) {
            $data['category2'] = (int)$_POST['category2'];
        }

        // функциональная группа
        $data['group'] = 0;
        if (ctype_digit($_POST['group'])) {
            $data['group'] = (int)$_POST['group'];
        }

        // производитель
        $data['maker'] = 0;
        if (ctype_digit($_POST['maker'])) {
            $data['maker'] = (int)$_POST['maker'];
        }

        // параметры подбора
        $data['params'] = array();
        if (isset($_POST['params']) && is_array($_POST['params'])) {
            foreach ($_POST['params'] as $key => $value) {
                $data['params'][$key] = array();
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $data['params'][$key][] = $k;
                    }
                }
            }
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование товара»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['maker'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Производитель»';
        }
        if (empty($data['code'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Код (артикул)»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            if (!empty($data['techdata'])) {
                $data['techdata'] = unserialize($data['techdata']);
            } else {
                $data['techdata'] = array();
            }
            // если была выбрана функциональная группа, мы должны получить от модели
            // массив параметров, привязанных к группе, массивы привязанных к этим
            // параметрам значений и передать эти данные в шаблон
            $data['allParams'] = $this->catalogBackendModel->getGroupParams($data['group']);
            // сохраняем введенные администратором данные в сессии
            $this->setSessionData('addCatalogProductForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['category'];

        // обращаемся к модели для добавления нового товара
        $this->catalogBackendModel->addProduct($data);

        return true;
    }

}