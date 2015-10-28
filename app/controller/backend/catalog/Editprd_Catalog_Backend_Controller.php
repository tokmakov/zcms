<?php
/**
 * Класс Editprd_Catalog_Backend_Controller для редактирования товара каталога,
 * формирует страницу с формой для редактирования товара, обновляет запись в
 * таблице БД products, работает с моделью Catalog_Backend_Model
 */
class Editprd_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного обновления товара и редиректа
     */
    protected $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editprd_Catalog_Backend_Controller
         */
        parent::input();

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление товара прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию отредактированного товара
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу каталога
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/editprd/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование товара. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // получаем от модели информацию о товаре
        $product = $this->catalogBackendModel->getProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->catalogBackendModel->getAllCategories();

        // получаем от модели массив всех функиональных групп, для возможности выбора
        $groups = $this->filterBackendModel->getGroups();

        // получаем от модели массив всех производителей, для возможности выбора
        $makers = $this->catalogBackendModel->getMakers();

        // единицы измерения для возможности выбора
        $units = array(
            1 => 'руб./шт.',
            2 => 'руб./компл.',
            3 => 'руб./упак.',
            4 => 'руб./метр',
            5 => 'руб./пара',
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,            // хлебные крошки
            // атрибут action тега form
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/editprd/id/' . $this->params['id']),
            'id'          => $this->params['id'],     // уникальный идентификатор товара
            'category'    => $product['category'],    // родительская категория
            'category2'   => $product['category2'],   // дополнительная категория
            'categories'  => $categories,             // массив всех категорий, для возможности выбора родителя
            'group'       => $product['group'],       // функциональная группа
            'groups'      => $groups,                 // массив всех функциональных групп, для возможности выбора
            'maker'       => $product['maker'],       // уникальный идентификатор производителя
            'makers'      => $makers,                 // массив всех производителей, для возможности выбора
            'code'        => $product['code'],        // код (артикул) товара
            'name'        => $product['name'],        // наименование изделия
            'title'       => $product['title'],       // функциональное наименование изделия
            'keywords'    => $product['keywords'],    // мета-тег keywords
            'description' => $product['description'], // мета-тег description
            'price'       => $product['price'],       // цена
            'unit'        => $product['unit'],        // единица измерения
            'units'       => $units,                  // все единицы измерения для возможности выбора
            'image'       => $product['image'],       // имя файла изображения
            'shortdescr'  => $product['shortdescr'],  // краткое описание
            'purpose'     => $product['purpose'],     // назначение изделия
            'features'    => $product['features'],    // особенности изделия
            'complect'    => $product['complect'],    // комплектация изделия
            'equipment'   => $product['equipment'],   // дополнительное оборудование
            'docs'        => $product['docs'],        // файлы документации
        );
        // технические характеристики
        $this->centerVars['techdata']    = array();
        if (!empty($product['techdata'])) {
            $this->centerVars['techdata'] = unserialize($product['techdata']);
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        // и сохраненные данные формы
        if ($this->issetSessionData('editCatalogProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editCatalogProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editCatalogProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет товар и возвращает true
     */
    protected function validateForm() {

        $data['name']        = trim(utf8_substr($_POST['name'], 0, 250));        // наименование изделия
        $data['title']       = trim(utf8_substr($_POST['title'], 0, 250));       // функциональное наименование изделия
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250));    // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['code']        = trim(utf8_substr($_POST['code'], 0, 16));         // код (артикул) товара
        $data['shortdescr']  = trim(utf8_substr($_POST['shortdescr'], 0, 2000)); // краткое описание
        $data['purpose']     = trim(utf8_substr($_POST['purpose'], 0, 4500));    // назначение изделия
        $data['features']    = trim(utf8_substr($_POST['features'], 0, 4500));   // особенности изделия
        $data['complect']    = trim(utf8_substr($_POST['complect'], 0, 4500));   // комплектация изделия
        $data['equipment']   = trim(utf8_substr($_POST['equipment'], 0, 4500));  // дополнительное оборудование

        // технические характеристики
        $data['techdata'] = '';
        foreach ($_POST['techdata_name'] as $name) {
            $tmp = trim(utf8_substr($name, 0, 250));
            $tmp = preg_replace('~\s+~', ' ', $tmp);
            $techdataName[] = $tmp;
        }
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

        // единица измерения
        $data['unit'] = 0;
        if (ctype_digit($_POST['unit']) && in_array($_POST['unit'], array(1,2,3,4,5))) {
            $data['unit'] = (int)$_POST['unit'];
        }

        // родительская категория
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category']; // новая родительская категория
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
        if (empty($data['unit'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Единица измерения»';
        }

        // были допущены ошибки при заполнении формы, сохраняем введенные
        // пользователем данные, чтобы после редиректа снова показать форму,
        // заполненную введенными ранее данными и сообщением об ошибке
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            if (!empty($data['techdata'])) {
                $data['techdata'] = unserialize($data['techdata']);
            } else {
                $data['techdata'] = array();
            }
            $this->setSessionData('editCatalogProductForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['category'];

        $data['id'] = $this->params['id']; // уникальный идентификатор товара

        // обращаемся к модели для обновления товара
        $this->catalogBackendModel->updateProduct($data);

        return true;
    }

}